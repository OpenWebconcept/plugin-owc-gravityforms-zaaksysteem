<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use DateTime;
use Exception;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use OWC\Zaaksysteem\Endpoints\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\PagedCollection;

abstract class AbstractCreateZaakAction
{
    public const CALLABLE_NAME = '';
    public const CLIENT_CATALOGI_URL = '';
    public const CLIENT_ZAKEN_URL = '';
    public const FORM_SETTING_SUPPLIER_KEY = '';

    protected function getApiClient(): Client
    {
        return ContainerResolver::make()->getApiClient(static::CALLABLE_NAME);
    }

    protected function getRSIN(): string
    {
        $rsin = ContainerResolver::make()->get('rsin');

        return ! empty($rsin) && is_string($rsin) ? $rsin : '';
    }

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
     * Use the selected `zaaktype identifier` to retrieve the `zaaktype`.
     *
     * @todo we cannot use the zaaktype URI to retrieve a zaaktype because it is bound to change when the zaaktype is updated. There doesn't seem to be a way to retrieve the zaaktype by identifier, so we have to get all the zaaktypen first and then filter them by identifier. We should change this when the API supports this.
     *
     * @see https://github.com/OpenWebconcept/plugin-owc-gravityforms-zaaksysteem/issues/13#issue-1697256063
     */
    public function getZaakType($form): ?Zaaktype
    {
        $client = $this->getApiClient();
        $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, static::FORM_SETTING_SUPPLIER_KEY)];

        return $client->zaaktypen()->byIdentifier($zaaktypeIdentifier);
    }

    /**
     * Merge mapped arguments with defaults.
     */
    protected function mappedArgs(string $rsin, Zaaktype $zaaktype, array $form, array $entry): array
    {
        $defaults = [
            'bronorganisatie' => $rsin,
            'informatieobject' => '',
            'omschrijving' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'verantwoordelijkeOrganisatie' => $rsin,
            'zaaktype' => $zaaktype['url'],
        ];

        return $this->mapArgs($defaults, $form, $entry);
    }

    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    protected function mapArgs(array $args, array $form, array $entry): array
    {
        foreach ($form['fields'] as $field) {
            if (empty($field->linkedFieldValueZGW) || ! isset($args[$field->linkedFieldValueZGW])) {
                continue;
            }

            $fieldValue = rgar($entry, (string) $field->id);

            if (empty($fieldValue)) {
                continue;
            }

            if ('date' === $field->type) {
                $fieldValue = (new \DateTime($fieldValue))->format('Y-m-d');
            }

            $args[$field->linkedFieldValueZGW] = $fieldValue;
        }

        return $args;
    }

    abstract public function addZaak($entry, $form): ?Zaak;

    /**
     * Add "zaak" properties.
     */
    public function addZaakEigenschappen(Zaak $zaak, $fields, $entry): void
    {
        $client = $this->getApiClient();
        $mapping = $this->mapZaakEigenschappenArgs($fields, $entry);

        foreach ($mapping as $value) {
            if (empty($value['eigenschap']) || empty($value['waarde'])) {
                continue;
            }

            $property = [
                'zaak' => $zaak->url,
                'eigenschap' => $value['eigenschap'],
                'waarde' => $value['waarde'],
            ];

            try {
                $client->zaakeigenschappen()->create(
                    $zaak,
                    new Zaakeigenschap($property, $client->getClientName(), $client->getClientNamePretty())
                );
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }
    }

    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    protected function mapZaakEigenschappenArgs(array $fields, array $entry): array
    {
        $mappedFields = [];

        foreach ($fields as $field) {
            if (empty($field->linkedFieldValueZGW) || strpos($field->linkedFieldValueZGW, 'https://') === false) {
                continue;
            }

            $property = \rgar($entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ('date' === $field->type) {
                try {
                    $property = (new DateTime($property))->format('Y-m-d');
                } catch (Exception $e) {
                    $property = '0000-00-00';
                }
            }

            $mappedFields[$field->id] = [
                'eigenschap' => $field->linkedFieldValueZGW,
                'waarde' => $property,
            ];
        }

        return $mappedFields;
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

        $currentBsn = resolve('digid.current_user_bsn');

        if (empty($currentBsn)) {
            throw new Exception('Deze sessie lijkt geen BSN te hebben');
        }

        $client = $this->getApiClient();

        foreach ($rolTypen as $rolType) {
            if ('initiator' !== $rolType['omschrijvingGeneriek']) {
                continue;
            }

            $args = [
                'betrokkeneIdentificatie' => [
                    'inpBsn' => $currentBsn,
                ],
                'betrokkeneType' => 'natuurlijk_persoon',
                'roltoelichting' => 'De indiener van de zaak.',
                'roltype' => $rolType['url'],
                'zaak' => $zaak->url,
            ];

            try {
                $rol = $client->rollen()->create(new Rol($args, $client->getClientName(), $client->getClientNamePretty()));
            } catch (Exception | BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }

        return $rol;
    }
}
