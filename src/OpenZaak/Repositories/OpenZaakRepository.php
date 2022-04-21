<?php declare(strict_types=1);

namespace OWC\OpenZaak\Repositories;

use OWC\OpenZaak\Models\OpenZaak as OpenZaakModel;

class OpenZaakRepository extends BaseRepository
{
    protected string $restURI = 'zaken/api/v1/zaken';

    public function __construct()
    {
        parent::__construct();
    }

    public function getZaken(): array
    {
        $result = $this->request($this->makeURL());

        if (empty($result['results'])) {
            return [];
        }

        return array_map(function ($zaak) {
            $model = new OpenZaakModel($zaak);

            if (empty($model->getStatusURL())) {
                return $model;
            }

            return $this->complementZaak($model);
        }, $result['results']);
    }

    protected function makeURL(): string
    {
        return sprintf('%s/%s', $this->baseURL, $this->restURI);
    }

    /**
     * Add data from other requests to OpenZaak object.
     */
    protected function complementZaak(OpenZaakModel $model): OpenZaakModel
    {
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

    protected function getStatus(OpenZaakModel $model): array
    {
        return $this->request($model->getStatusURL());
    }

    protected function getDetailedStatus(OpenZaakModel $model): array
    {
        return $this->request($model->getStatusTypeURL());
    }
}
