<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\OpenZaak;

use Exception;

use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;

use function OWC\Zaaksysteem\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;

class ZaakRepository extends BaseRepository
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    /**
     * Construct the repository.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct();

        $this->plugin = $plugin;
    }

    /**
     * Get the api client.
     */
    protected function getApiClient(): Client
    {
        return $this->plugin->getContainer()->get('oz.client');
    }

    protected function handleArgs(string $identifier, array $fields, array $entry)
    {
        $rsin = $this->plugin->getContainer()->get('rsin');

        if (empty($rsin)) {
            throw new Exception(
                esc_html__(
                    'RSIN should not be empty in the Gravity Forms Settings',
                    config('core.text_domain')
                )
            );
        }

        $args = [
            'bronorganisatie' => $rsin ?? '',
            'verantwoordelijkeOrganisatie' => $rsin ?? '',
            'zaaktype' => $identifier ?? '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'omschrijving' => '',
            'informatieobject' => ''
        ];

        return $this->mapArgs($args, $fields, $entry);
    }

    /**
     * Get all available roles.
     */
    public function getRolTypen(): PagedCollection
    {
        $client = $this->getApiClient();
        return $client->roltypen()->all();
    }

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): void
    {
        $client = $this->getApiClient();
        $identifier = $form['owc-gravityforms-zaaksysteem-form-setting-openzaak-identifier'];
        $args = $this->handleArgs($identifier, $form['fields'], $entry);

        $zaak = $client->zaken()->create(new Zaak($args, 'test'));

        $this->addRolToZaak($zaak['url']);
    }

    /**
     * Assign a submitter to the "zaak".
     *
     * @todo: change createSubmitter
     */
    public function addRolToZaak(string $zaakUrl): void
    {
        $client = $this->getApiClient();
        $rolTypen = $this->getRolTypen();

        foreach ($rolTypen as $rolType) {
            if ($rolType['omschrijvingGeneriek'] !== 'initiator') {
                continue;
            }

            $args = [
                'zaak' => $zaakUrl,
                'betrokkeneType' => 'natuurlijk_persoon',
                'roltype' => $rolType['url'],
                'roltoelichting' => 'De indiener van de zaak.',
                'betrokkeneIdentificatie' => [
                    'inpBsn' => $this->resolveCurrentBsn()
                ]
            ];

            try {
                $client->rollen()->create(new Rol($args, 'test'));
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }
    }

    /**
     * @todo move this to separate handler
     */
    protected function resolveCurrentBsn(): string
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn');

        return decrypt($bsn);
    }
}
