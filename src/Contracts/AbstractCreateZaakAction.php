<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use DateTime;
use Exception;
use OWC\Zaaksysteem\Endpoints\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Entities\Zaaktype;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Traits\FormSetting;

abstract class AbstractCreateZaakAction
{
    use FormSetting;

    public const CLIENT_NAME = '';
    public const CALLABLE_NAME = '';
    public const CLIENT_CATALOGI_URL = '';
    public const CLIENT_ZAKEN_URL = '';
    public const FORM_SETTING_SUPPLIER_KEY = '';

    protected function getApiClient(): Client
    {
        return ContainerResolver::make()->getApiClient(static::CLIENT_NAME);
    }

    protected function getRSIN(): string
    {
        return ContainerResolver::make()->rsin();
    }

    /**
     * Get all available "roltypen".
     */
    public function getRolTypen(string $zaaktypeURL): PagedCollection
    {
        $client = $this->getApiClient();

        $filter = new RoltypenFilter();
        $filter->add('zaaktype', $zaaktypeURL);

        return $client->roltypen()->filter($filter);
    }

    /**
     * Use the selected `zaaktype identifier` to compose the url to the 'zaaktype'.
     */
    public function getZaakTypeURL($form): ?string
    {
        $client = $this->getApiClient();
        $zaaktypeIdentifier = $this->zaaktypeIdentifierFormSetting($form, static::FORM_SETTING_SUPPLIER_KEY);

        if (empty($zaaktypeIdentifier)) {
            return null;
        }

        /**
         * In previous versions the UUID of a 'Zaaktype' was saved instead of its URL.
         * This check is here to support backwards compatibility.
         */
        if (! filter_var($zaaktypeIdentifier, FILTER_VALIDATE_URL)) {
            return sprintf('%s/%s/%s', untrailingslashit($client->getEndpointUrlByType('catalogi')), 'zaaktypen', $zaaktypeIdentifier);
        }

        return $zaaktypeIdentifier;
    }

    /**
     * Merge mapped arguments with defaults.
     */
    protected function mappedArgs(string $rsin, string $zaaktypeURL, array $form, array $entry): array
    {
        $defaults = [
            'bronorganisatie' => $rsin,
            'omschrijving' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'verantwoordelijkeOrganisatie' => $rsin,
            'zaaktype' => $zaaktypeURL,
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

    abstract public function addZaak($entry, $form): Zaak;

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
    public function addRolToZaak(Zaak $zaak, string $zaaktypeURL): ?Rol
    {
        $rolTypen = $this->getRolTypen($zaaktypeURL);

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

            break;
        }

        return $rol ?? null;
    }
}
