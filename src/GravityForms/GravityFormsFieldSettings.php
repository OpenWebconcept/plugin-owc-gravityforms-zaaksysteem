<?php

namespace OWC\Zaaksysteem\GravityForms;

use Exception;
use OWC\Zaaksysteem\Endpoints\Filter\EigenschappenFilter;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Informatieobjecttype;
use OWC\Zaaksysteem\Entities\Zaaktype;
use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Support\PagedCollection;

class GravityFormsFieldSettings
{
    /**
     * Use the selected `zaaktype identifier` to retrieve the `zaaktype`.
     *
     * @todo we cannot use the zaaktype URI to retrieve a zaaktype because it is bound to change when the zaaktype is updated. There doesn't seem to be a way to retrieve the zaaktype by identifier, so we have to get all the zaaktypen first and then filter them by identifier. We should change this when the API supports this.
     *
     * @see https://github.com/OpenWebconcept/plugin-owc-gravityforms-zaaksysteem/issues/13#issue-1697256063
     */
    public function getZaakType(string $supplier, string $zaaktypeIdentifier): ?Zaaktype
    {
        $client = ContainerResolver::make()->getApiClient($supplier);

        return $client->zaaktypen()->byIdentifier($zaaktypeIdentifier);
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

    public function getInformatieObjectTypen(string $supplier)
    {
        $client = ContainerResolver::make()->getApiClient($supplier);
        $transientKey = sprintf('%s-form-field-mapping-information-object-type', $client->getClientNamePretty()); // Unique transient key.
        $types = get_transient($transientKey);

        if (is_array($types) && $types) {
            return $types;
        }

        $page = 1;
        $types = [];

        while ($page) {
            try {
                $result = $client->informatieobjecttypen()->all((new ResultaattypenFilter())->page($page));
            } catch (Exception $e) {
                break;
            }

            $types = array_merge($types, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        if (empty($types)) {
            return [];
        }

        set_transient($transientKey, $types, 64800); // 18 hours.

        return $types;
    }

    protected function prepareObjectTypesOptions(array $types): array
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
        $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, $supplier)];

        // Get the zaaktype belonging to the chosen zaaktype identifier.
        $zaaktype = $this->getZaakType($supplier, $zaaktypeIdentifier);

        if (empty($zaaktype['url'])) {
            $properties = [];
        } else {
            $properties = $this->getZaaktypenEigenschappen($supplier, $zaaktype->url);
        }

        echo view('partials/gf-field-options.php', [
            'properties' => $properties instanceof PagedCollection ? $this->preparePropertiesOptions($properties) : [],
            'objecttypes' => $this->prepareObjectTypesOptions($this->getInformatieObjectTypen($supplier)),
        ]);
    }
}
