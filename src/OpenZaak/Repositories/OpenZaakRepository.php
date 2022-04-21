<?php declare(strict_types=1);

namespace OWC\OpenZaak\Repositories;

use OWC\OpenZaak\Models\OpenZaak as OpenZaakModel;
use OWC\OpenZaak\Models\StatusType as StatusTypeModel;

class OpenZaakRepository extends BaseRepository
{
    protected string $zakenURI = 'zaken/api/v1/zaken';
    protected string $catalogiURI = 'catalogi/api/v1/statustypen';

    public function __construct()
    {
        parent::__construct();
    }

    public function getZaken(): array
    {
        $result = $this->request($this->makeURL($this->zakenURI));

        if (empty($result['results'])) {
            return [];
        }

        return array_map(function ($zaak) {
            $model = new OpenZaakModel($zaak); // makeFrom
            return $this->complementZaak($model);
        }, $result['results']);
    }

    protected function makeURL(string $uri = ''): string
    {
        return sprintf('%s/%s', $this->baseURL, $uri);
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
        $types = $this->request($this->makeURL($this->catalogiURI));

        if (empty($types['results']) || !is_array($types['results'])) {
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
