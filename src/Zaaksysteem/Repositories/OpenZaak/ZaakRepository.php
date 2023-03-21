<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\OpenZaak;

use OWC\Zaaksysteem\Models\OpenZaak as OpenZaakModel;
use OWC\Zaaksysteem\Models\StatusType as StatusTypeModel;

use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

class ZaakRepository extends BaseRepository
{
    protected string $zakenURI = 'zaken/api/v1/zaken';
    protected string $catalogiStatusTypen = 'catalogi/api/v1/statustypen';

    public function __construct()
    {
        parent::__construct();
    }

    public function getBsn(): string
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn');
        return decrypt($bsn);
    }

    public function getZaken(): array
    {
        if (empty($this->getBsn())) {
            return [];
        }

        $result = $this->request($this->makeURL($this->zakenURI . '?rol__betrokkeneIdentificatie__natuurlijkPersoon__inpBsn=' . $this->getBsn()));

        if (empty($result['results'])) {
            return [];
        }

        return array_map(function ($zaak) {
            $model = new OpenZaakModel($zaak); // makeFrom
            return $this->complementZaak($model);
        }, $result['results']);
    }

    /**
     * Add data from other requests to OpenZaak object.
     */
    protected function complementZaak(OpenZaakModel $model): OpenZaakModel
    {
        $model->setStatusTypes($this->getStatusTypes($model));
        $status = $this->getStatus($model);

        if (empty($status)) {
            return $model;
        }

        try {
            $model->setDateStatusAssigned($status['datumStatusGezet'] ?? '');
            $model->setStatusTypeURL($status['statustype'] ?? '');
        } catch (\Exception | \TypeError $e) {
            return $model;
        }

        $detailedStatus = $this->getDetailedStatus($model);

        if (empty($detailedStatus)) {
            return $model;
        }

        try {
            $model->setStatusDesc($detailedStatus['omschrijving'] ?? '');
        } catch (\Exception | \TypeError $e) {
            return $model;
        }

        return $model;
    }

    protected function getStatusTypes(OpenZaakModel $model): array
    {
        $types = $this->request($this->makeURL($this->catalogiStatusTypen));

        if (empty($types['results']) || ! is_array($types['results'])) {
            return [];
        }

        $types = array_map(function ($type) {
            return new StatusTypeModel($type);
        }, $types['results']);

        $types = array_filter($types, function ($type) use ($model) {
            return $model->getTypeURL() === $type->getType();
        });

        usort($types, function ($type, $type2) {
            return $type->getNumber() <=> $type2->getNumber();
        });

        return $types;
    }

    protected function getStatus(OpenZaakModel $model): array
    {
        return $this->request($model->getStatusURL());
    }

    protected function getDetailedStatus(OpenZaakModel $model): array
    {
        return $this->request($model->getStatusTypeURL());
    }
}
