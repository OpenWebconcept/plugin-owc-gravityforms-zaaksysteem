<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\RxMission\Actions;

use Exception;
use OWC\Zaaksysteem\Contracts\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Endpoints\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Support\PagedCollection;

use function OWC\Zaaksysteem\Foundation\Helpers\field_mapping;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CALLABLE_NAME = 'rx.client';
    public const CLIENT_CATALOGI_URL = 'rx.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'rx.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'rx-mission';

    /**
     * Get all available "roltypen".
     */
    public function getRolTypen(string $zaaktype): PagedCollection
    {
        $client = $this->getApiClient();

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
            'omschrijving' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'verantwoordelijkeOrganisatie' => $rsin,
            'zaaktype' => $zaaktype['url']
        ];

        $client = $this->getApiClient();

        $zaak = $client->zaken()->create(new Zaak($args, $client->getClientName()));

        $this->addRolToZaak($zaak, $zaaktype['url']);
        $this->addZaakEigenschappen($zaak, $form['fields'], $entry);

        return $zaak;
    }

    /**
     * Add "zaak" properties.
     */
    public function addZaakEigenschappen(Zaak $zaak, $fields, $entry): void
    {
        $client = $this->getApiClient();
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
                    new Zaakeigenschap($property, $client->getClientName())
                );
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }
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

        return $rol;
    }
}
