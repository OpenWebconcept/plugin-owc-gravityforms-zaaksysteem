<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Actions\Roxit;

use Exception;

use function OWC\Zaaksysteem\Foundation\Helpers\field_mapping;

use OWC\Zaaksysteem\Actions\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Endpoint\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Support\PagedCollection;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CALLABLE_NAME = 'ro.client';
    public const CLIENT_CATALOGI_URL = 'ro.catalogi_url';
    public const CLIENT_ZAKEN_URL = 'ro.zaken_url';
    public const FORM_SETTING_SUPPLIER_KEY = 'roxit';

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

        $zaak = $client->zaken()->create(new Zaak($args, $client::CLIENT_NAME));

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
        $client->setEndpointURL($this->getZakenURL());
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

        return $rol;
    }
}
