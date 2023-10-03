<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin\Actions;

use Exception;
use OWC\Zaaksysteem\Clients\DecosJoin\Client;
use OWC\Zaaksysteem\Contracts\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Endpoints\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Support\PagedCollection;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CALLABLE_NAME = 'dj.client';
    public const CLIENT_CATALOGI_URL = 'dj.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'dj.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'decos-join';

    public function getRolTypen(string $zaaktype): PagedCollection
    {
        $client = $this->getApiClient();

        $filter = new RoltypenFilter();
        $filter->add('zaaktype', $zaaktype);

        return $client->roltypen()->filter($filter);
    }

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): ?Zaak
    {
        $rsin = $this->plugin->getContainer()->get('rsin');

        if (empty($rsin)) {
            throw new Exception('Het RSIN is niet ingesteld in de Gravity Forms instellingen');
        }

        $zaaktype = $this->getZaakType($form);

        if (empty($zaaktype)) {
            throw new Exception('Het zaaktype is niet ingesteld in de Gravity Forms instellingen');
        }

        $client = $this->getApiClient();
        $client = $this->setAuthenticatorSecretZRC($client); // Another secret is required for talking to this specific API.

        $args = $this->mappedArgs($rsin, $zaaktype, $form, $entry);
        $zaak = $client->zaken()->create(new Zaak($args, $client->getClientName()));

        $client = $this->setAuthenticatorSecretZTC($client); // Restore default secret.

        // REFERENCE POINT: Mike, adding 'Rol' and 'Zaak Eigenschappen' does not work yet.
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
        $client = $this->setAuthenticatorSecretZRC($client);

        $mapping = $this->mapZaakEigenschappenArgs($fields, $entry);

        foreach ($mapping as $value) {
            $property = [
                'eigenschap' => $value['eigenschap'],
                'waarde' => $value['waarde'],
                'zaak' => $zaak->uri,
            ];

            try {
                $client->zaakeigenschappen()->create(
                    $zaak,
                    new Zaakeigenschap($property, $client->getClientName())
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

        if ($rolTypen->isEmpty()) {
            throw new Exception('Er zijn geen roltypen gevonden voor dit zaaktype');
        }

        $currentBsn = $this->resolveCurrentBsn();

        if (empty($currentBsn)) {
            throw new Exception('Deze sessie lijkt geen BSN te hebben');
        }

        $client = $this->getApiClient();
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
                $rol = $client->rollen()->create(new Rol($args, $client->getClientName()));
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }

        $client = $this->setAuthenticatorSecretZTC($client);

        return $rol;
    }

    /**
     * Token for the 'ZTC' api.
     */
    private function setAuthenticatorSecretZTC(Client $client): Client
    {
        $secret = $this->plugin->getContainer()->get('dj.client_secret');
        $client->getAuthenticator()->setClientSecret($secret);

        return $client;
    }

    /**
     * Token for the 'ZRC' api.
     */
    private function setAuthenticatorSecretZRC(Client $client): Client
    {
        $secret = $this->plugin->getContainer()->get('dj.client_secret_zrc');
        $client->getAuthenticator()->setClientSecret($secret);

        return $client;
    }
}
