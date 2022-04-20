<?php declare(strict_types=1);

namespace OWC\OpenZaak\Repositories;

use OWC\OpenZaak\Models\OpenZaak as OpenZaakModel;

class OpenZaakRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getZaken(): array
    {
        $result = $this->request($this->url);

        return array_map(function ($zaak) {
            return new OpenZaakModel($zaak);
        }, $result);
    }
}
