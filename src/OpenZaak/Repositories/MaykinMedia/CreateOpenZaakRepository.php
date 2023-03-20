<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Repositories\MaykinMedia;

use function OWC\OpenZaak\Foundation\Helpers\decrypt;

class CreateOpenZaakRepository extends BaseRepository
{
    protected string $zakenURI = 'zaken/api/v1/zaken';

    protected string $zakenRolURI = 'zaken/api/v1/rollen';

    protected string $catalogiRolTypenURI = 'catalogi/api/v1/roltypen';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * TODO: request RolType based on ZAAKTYPE
     */
    protected function getRoleTypes()
    {
        return $this->request($this->makeURL($this->catalogiRolTypenURI));
    }

    /**
     * Add a `rol` to a `zaak`.
     */
    public function createSubmitter(string $zaakUrl, string $bsn): array
    {
        if (empty($zaakUrl) || empty($bsn)) {
            return [];
        }

        $roleTypes = $this->getRoleTypes();

        if (empty($roleTypes['results'])) {
            return [];
        }

        foreach ($roleTypes['results'] as $roleType) {
            if ($roleType['omschrijving'] !== 'Initiator') {
                continue;
            }

            $data = [
                'zaak'              => $zaakUrl,
                'betrokkeneType'    => 'natuurlijk_persoon',
                'roltype'           => $roleType['url'],
                'roltoelichting'    => 'De indiener van de zaak.',
                'betrokkeneIdentificatie' => [
                    'inpBsn' => decrypt($bsn)
                ]
            ];

            return $this->request($this->makeURL($this->zakenRolURI), 'POST', $data);
        }

        return [];
    }

    public function createOpenZaak(array $args = []): array
    {
        return $this->request($this->makeURL($this->zakenURI), 'POST', $args);
    }
}
