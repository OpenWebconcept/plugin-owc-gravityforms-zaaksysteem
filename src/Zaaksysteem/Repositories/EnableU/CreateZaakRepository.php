<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU;

class CreateZaakRepository extends BaseRepository
{
    protected string $zakenURI = 'zaken/api/v1/zaken';
    protected string $informationObjectsURI = 'zaken/api/v1/enkelvoudiginformatieobjecten';

    public function __construct()
    {
        parent::__construct();
    }

    public function createOpenZaak(array $args = []): array
    {
        return $this->request($this->makeURL($this->zakenURI), 'POST', $args);
    }

    public function addInformationObjectToZaak()
    {
        // Needs to be implemented.
    }
}
