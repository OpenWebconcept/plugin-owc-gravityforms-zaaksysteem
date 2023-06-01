<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Actions\DecosJoin;

use Exception;

use function OWC\Zaaksysteem\Foundation\Helpers\field_mapping;

use OWC\Zaaksysteem\Actions\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Endpoint\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Support\PagedCollection;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CALLABLE_NAME = 'dj.client';
    public const CLIENT_CATALOGI_URL = 'dj.catalogi_url';
    public const CLIENT_ZAKEN_URL = 'dj.zaken_url';
    public const FORM_SETTING_SUPPLIER_KEY = 'decos-join';

    public function getRolTypen(string $zaaktype): PagedCollection
    {
        $client = $this->getApiClient();
        $client->setEndpointURL($this->getCatalogiURL());

        $filter = new RoltypenFilter();
        $filter->get('zaaktype', $zaaktype);

        return $client->roltypen()->filter($filter);
    }

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): ?Zaak
    {
        $rsin = $this->plugin->getContainer()->get('rsin');
        $zaaktype = $this->getZaakType($form);

        if (empty($rsin)) {
            throw new Exception('Het RSIN is niet ingesteld in de Gravity Forms instellingen');
        }

        if (empty($zaaktype)) {
            throw new Exception('Het zaaktype is niet ingesteld in de Gravity Forms instellingen');
        }

        $args = [
            'bronorganisatie' => $rsin,
            'informatieobject' => '',
            'omschrijving' => '', // TODO: add form name
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'verantwoordelijkeOrganisatie' => $rsin,
            'zaaktype' => $zaaktype['url']
        ];

        $client = $this->getApiClient();
        $client->setEndpointURL($this->getZakenURL());

        $client = $this->setAuthenticatorSecretZRC($client);
        $zaak = $client->zaken()->create(new Zaak($args, $client::CLIENT_NAME));
        $client = $this->setAuthenticatorSecretZTC($client);

        // REFERENCE POINT: Mike
        // Maybe add the switching of tokens inside the constructor of Endpoint.php, check on $this->client and api-type zaken? or wakeup en sleep magic methods?
        // $this->addRolToZaak($zaak, $zaaktype['url']); -> returns 'Bad request "zaaktype mandatory parameter not provided."
        // $this->addZaakEigenschappen($zaak, $form['fields'], $entry); -> returns 'Bad request "zaaktype mandatory parameter not provided."

        return $zaak;
    }

    /**
     * Add "zaak" properties.
     */
    public function addZaakEigenschappen(Zaak $zaak, $fields, $entry): void
    {
        $client = $this->getApiClient();
        $client->setEndpointURL($this->getZakenURL());
        $client = $this->setAuthenticatorSecretZRC($client);

        $mapping = field_mapping($fields, $entry);

        foreach ($mapping as $value) {
            $property = [
                'eigenschap' => $value['eigenschap'],
                'waarde' => $value['waarde'],
                'zaak' => $zaak->uri,
            ];

            try {
                $client->zaakeigenschappen()->create(
                    $zaak,
                    new Zaakeigenschap($property, $client::CLIENT_NAME)
                );
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }

        $client = $this->setAuthenticatorSecretZTC($client);
    }

    /**
     * Assign a submitter to the "zaak".
     */
    public function addRolToZaak(Zaak $zaak, string $zaaktype): ?Rol
    {
        $rolTypen = $this->getRolTypen($zaaktype);
        $rol = null;

        $currentBsn = $this->resolveCurrentBsn();

        if ($rolTypen->isEmpty()) {
            throw new Exception('Er zijn geen roltypen gevonden voor dit zaaktype');
        }

        if (empty($currentBsn)) {
            throw new Exception('Deze sessie lijkt geen BSN te hebben');
        }

        $client = $this->getApiClient();
        $client->setEndpointURL($this->getZakenURL());
        $client = $this->setAuthenticatorSecretZRC($client);
        
        foreach ($rolTypen as $rolType) {
            if ($rolType['omschrijvingGeneriek'] !== 'initiator') {
                continue;
            }

            $args = [
                'betrokkeneIdentificatie' => [
                    'inpBsn' => $currentBsn
                ],
                'betrokkeneType' => 'natuurlijk_persoon',
                'roltoelichting' => 'De indiener van de zaak.',
                'roltype' => $rolType['url'],
                'zaak' => $zaak->url
            ];

            try {
                $rol = $client->rollen()->create(new Rol($args, $client::CLIENT_NAME));
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }

        $client = $this->setAuthenticatorSecretZTC($client);

        return $rol;
    }

    /**
     * Token for the 'catalogi' api.
     */
    private function setAuthenticatorSecretZTC(Client $client): Client
    {
        $secret = $this->plugin->getContainer()->get('dj.client_secret');
        $client->getAuthenticator()->setClientSecret($secret);

        return $client;
    }

    /**
     * Token for the 'zaken' api.
     */
    private function setAuthenticatorSecretZRC(Client $client): Client
    {
        $secret = $this->plugin->getContainer()->get('dj.client_secret_zrc');
        $client->getAuthenticator()->setClientSecret($secret);

        return $client;
    }
}
