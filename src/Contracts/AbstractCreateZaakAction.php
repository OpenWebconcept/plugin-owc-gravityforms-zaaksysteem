<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use DateTime;
use Exception;
use OWC\Zaaksysteem\Endpoints\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Traits\FormSetting;
use OWC\Zaaksysteem\Traits\MergeTags;

abstract class AbstractCreateZaakAction
{
    use FormSetting;
    use MergeTags;

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
                $fieldValue = (new DateTime($fieldValue))->format('Y-m-d');
            }

            $args[$field->linkedFieldValueZGW] = $this->handleMergeTags($entry, $form, $fieldValue);
        }

        return $args;
    }

    protected function getPossibleBranchNumberKVK(array $form, array $entry): string
    {
        foreach ($form['fields'] as $field) {
            if (! isset($field->linkedFieldValueBranchNumberKVK) || '1' !== $field->linkedFieldValueBranchNumberKVK) {
                continue;
            }

            $fieldValue = rgar($entry, (string) $field->id);

            if (is_string($fieldValue) || '' !== $fieldValue) {
                return $fieldValue;
            }
        }

        return '';
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
            } catch (Exception $e) {
                // Fail silently.
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
    public function addRolToZaak(Zaak $zaak, string $zaaktypeURL, string $branchNumberKVK = ''): ?Rol
    {
        $rolTypen = $this->getRolTypen($zaaktypeURL);

        if ($rolTypen->isEmpty()) {
            throw new Exception('Er zijn geen roltypen gevonden voor dit zaaktype');
        }

        $currentBsn = resolve('digid.current_user_bsn');
        $currentKVK = resolve('eherkenning.current_user_kvk');

        if (empty($currentBsn) && empty($currentKVK)) {
            throw new Exception('Deze sessie lijkt geen BSN nummer of KVK nummer te hebben');
        }

        $client = $this->getApiClient();

        foreach ($rolTypen as $rolType) {
            if ('initiator' !== $rolType['omschrijvingGeneriek']) {
                continue;
            }

            $args = [
                'roltoelichting' => $rolType['omschrijvingGeneriek'],
                'roltype' => $rolType['url'],
                'zaak' => $zaak->url,
            ];

            if (is_string($currentBsn) && '' !== $currentBsn) {
                $args['betrokkeneIdentificatie']['inpBsn'] = $currentBsn;
                $args['betrokkeneType'] = 'natuurlijk_persoon';
            } elseif (is_string($currentKVK) && '' !== $currentKVK) {
                $args['betrokkeneIdentificatie']['kvkNummer'] = $currentKVK;
                $args['betrokkeneType'] = 'vestiging';
                $args['betrokkeneIdentificatie']['vestigingsNummer'] = $branchNumberKVK;
            }

            try {
                $rol = $client->rollen()->create(new Rol($args, $client->getClientName(), $client->getClientNamePretty()));
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            } catch (Exception $e) {
                // Fail silently.
            }

            break;
        }

        return $rol ?? null;
    }
}
