<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use DateTime;
use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Traits\ResolveBSN;
use OWC\Zaaksysteem\Traits\ZaakTypeByIdentifier;

abstract class AbstractCreateZaakAction
{
    use ResolveBSN;
    use ZaakTypeByIdentifier;

    public const CALLABLE_NAME = '';
    public const CLIENT_CATALOGI_URL = '';
    public const CLIENT_ZAKEN_URL = '';
    public const FORM_SETTING_SUPPLIER_KEY = '';

    protected Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    protected function getApiClient(): Client
    {
        return $this->plugin->getContainer()->get(static::CALLABLE_NAME);
    }

    abstract public function getRolTypen(string $zaaktype): PagedCollection;

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

        return $this->zaakTypeByIdentifier($client, $zaaktypeIdentifier);
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
            'zaaktype' => $zaaktype['url']
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

            if ($field->type === 'date') {
                $fieldValue = (new \DateTime($fieldValue))->format('Y-m-d');
            }

            $args[$field->linkedFieldValueZGW] = $fieldValue;
        }

        return $args;
    }

    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    protected function mapZaakEigenschappenArgs(array $fields, array $entry): array
    {
        $mappedFields = [];

        foreach ($fields as $field) {
            if (empty($field->linkedFieldValueZGW)) {
                continue;
            }

            $property = \rgar($entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ($field->type === 'date') {
                try {
                    $property = (new DateTime($property))->format('Y-m-d');
                } catch (Exception $e) {
                    $property = '0000-00-00';
                }
            }

            $mappedFields[$field->id] = [
                'eigenschap' => $field->linkedFieldValueZGW,
                'waarde' => $property
            ];
        }

        return $mappedFields;
    }

    abstract public function addZaak($entry, $form): ?Zaak;
    abstract public function addZaakEigenschappen(Zaak $zaak, $fields, $entry): void;
    abstract public function addRolToZaak(Zaak $zaak, string $zaaktype): ?Rol;
}
