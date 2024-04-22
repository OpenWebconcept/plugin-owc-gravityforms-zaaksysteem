<?php

namespace OWC\Zaaksysteem\GravityForms;

use Exception;
use OWC\Zaaksysteem\Endpoints\Filter\EigenschappenFilter;
use OWC\Zaaksysteem\Entities\Informatieobjecttype;
use OWC\Zaaksysteem\Entities\Zaaktype;
use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Traits\FormSetting;

class GravityFormsFieldSettings
{
    use FormSetting;

    protected const TRANSIENT_LIFETIME_IN_SECONDS = 64800; // 18 hours.

    /**
     * Use the selected `zaaktype identifier` to retrieve the `zaaktype`.
     *
     * @todo we cannot use the zaaktype URI to retrieve a zaaktype because it is bound to change when the zaaktype is updated. There doesn't seem to be a way to retrieve the zaaktype by identifier, so we have to get all the zaaktypen first and then filter them by identifier. We should change this when the API supports this.
     *
     * @see https://github.com/OpenWebconcept/plugin-owc-gravityforms-zaaksysteem/issues/13#issue-1697256063
     */
    public function getZaakType(string $supplier, string $zaaktypeIdentifier): ?Zaaktype
    {
        $transientKey = sprintf('%s-%s', sanitize_title($supplier), sanitize_title($zaaktypeIdentifier));
        $zaaktype = get_transient($transientKey);

        if ($zaaktype instanceof Zaaktype) {
            return $zaaktype;
        }

        $client = ContainerResolver::make()->getApiClient($supplier);

        try {
            $zaaktype = $this->getZaaktypeByClient($client, $zaaktypeIdentifier);
        } catch(Exception $e) {
            $zaaktype = null;
        }

        if (! $zaaktype instanceof Zaaktype) {
            return null;
        }

        set_transient($transientKey, $zaaktype, self::TRANSIENT_LIFETIME_IN_SECONDS);

        return $zaaktype;
    }

    /**
     * Decos API is very slow.
     * For demostration purposes we match on 'Zaaktype' identifier to ensure some speed.
     */
    protected function getZaaktypeByClient($client, string $zaaktypeIdentifier): ?Zaaktype
    {
        /**
         * In previous versions the UUID of a 'Zaaktype' was saved instead of its URL.
         * This check takes the last part of the URL, the identifier, and is here to support backwards compatibility.
         */
        if (filter_var($zaaktypeIdentifier, FILTER_VALIDATE_URL)) {
            $explode = explode('/', $zaaktypeIdentifier) ?: [];
            $zaaktypeIdentifier = end($explode);
        }

        $zaaktype = $client->zaaktypen()->get($zaaktypeIdentifier);

        /**
         * When the API supports filtering on zaaktype identification this line should be used.
         * Fow now the 'byIdentifier' method is quite memory-intensive.
         */
        // $zaaktype = $client->zaaktypen()->byIdentifier($zaaktypeIdentifier);

        return $zaaktype;
    }

    /**
     * Get the `zaakeigenschappen` belonging to the chosen `zaaktype`.
     */
    public function getZaaktypenEigenschappen(string $supplier, string $zaaktypeUrl): PagedCollection
    {
        $client = ContainerResolver::make()->getApiClient($supplier);

        $filter = new EigenschappenFilter();
        $filter->add('zaaktype', $zaaktypeUrl);

        return $client->eigenschappen()->filter($filter);
    }

    protected function preparePropertiesOptions(PagedCollection $properties): array
    {
        $options = $properties->map(function ($property) {
            if (empty($property['naam']) || empty($property['url'])) {
                return [];
            }

            return [
                'label' => $property['naam'],
                'value' => $property['url'],
            ];
        })->toArray();

        return array_filter((array) $options);
    }
    public function getInformatieObjectTypen(Zaaktype $zaaktype, $zaaktypeIdentification)
    {
        $transientKey = sprintf('zaaktype-%s-mapping-information-object-types', sanitize_title($zaaktypeIdentification));
        $types = get_transient($transientKey);

        if (is_array($types) && $types) {
            return $types;
        }

        $types = $zaaktype->informatieobjecttypen->all();

        if (empty($types)) {
            return [];
        }

        set_transient($transientKey, $types, self::TRANSIENT_LIFETIME_IN_SECONDS);

        return $types;
    }

    protected function prepareObjectTypesOptions(array $types, $zaaktypeIdentification): array
    {
        if (empty($types)) {
            return [];
        }

        return (array) Collection::collect($types)->map(function (Informatieobjecttype $objecttype) {
            return [
                'label' => "{$objecttype->omschrijving} ({$objecttype->vertrouwelijkheidaanduiding})",
                'value' => $objecttype->url,
            ];
        })->all();
    }

    /**
     * Add script to the editor of a form.
     * Script adds chosen value from custom select to field object which can be used after the form submission.
     */
    public function addSelectScript(): void
    {
        echo view('scriptSelect.php');
    }

    /**
     * Add custom select to Gravity Form fields.
     * Used for mapping a field to a supplier setting.
     */
    public function addSelect($position, $formId): void
    {
        if (! class_exists('\GFAPI')) {
            return;
        }

        $form = \GFAPI::get_form($formId);
        $supplier = get_supplier($form, true);

        if (0 !== $position || empty($supplier)) {
            return;
        }

        // Get the selected zaaktype identifier from the form's settings.
        $zaaktypeIdentifier = $this->zaaktypeIdentifierFormSetting($form, $supplier);

        // Get the zaaktype belonging to the chosen zaaktype identifier.
        $zaaktype = $this->getZaakType($supplier, $zaaktypeIdentifier);

        // Without a zaaktype there is no point in continuing.
        if (! $zaaktype) {
            return;
        }

        if (empty($zaaktype['url'])) {
            $properties = [];
        } else {
            $properties = $this->getZaaktypenEigenschappen($supplier, $zaaktype->url);
        }

        $zaaktypeIdentification = $zaaktype->identificatie;

        echo view('partials/gf-field-options.php', [
            'properties' => $properties instanceof PagedCollection ? $this->preparePropertiesOptions($properties) : [],
            'objecttypes' => $this->prepareObjectTypesOptions($this->getInformatieObjectTypen($zaaktype, $zaaktypeIdentification), $zaaktypeIdentification),
        ]);
    }
}
