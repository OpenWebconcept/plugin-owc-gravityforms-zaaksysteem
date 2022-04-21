<?php declare(strict_types=1);

namespace OWC\OpenZaak\Repositories;

class CreateOpenZaakRepository extends BaseRepository
{
    protected string $zakenURI = 'zaken/api/v1/zaken';

    public function __construct()
    {
        parent::__construct();
    }

    public function createOpenZaak(array $args = []): array
    {
        return $this->request($this->makeURL($this->zakenURI), 'POST', $args);
    }
}
