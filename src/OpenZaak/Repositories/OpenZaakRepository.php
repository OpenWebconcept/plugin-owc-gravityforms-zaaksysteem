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

        if (empty($result)) {
            return [];
        }

        return array_map(function ($zaak) {
            return new OpenZaakModel($zaak);
        }, $result);
    }

    protected function makeURL(): string
    {
        return sprintf('%s/%s', $this->baseURL, $this->restURI);
    }
}
