<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Endpoint\Filter\EigenschappenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Support\PagedCollection;

use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class GravityFormsFieldSettings
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Get the api client.
     *
     * @todo make generic, so we can use it for Decos Join as well.
     */
    protected function getApiClient(): Client
    {
        return $this->plugin->getContainer()->get('ro.client');
    }

    /**
     * Use the selected `zaaktype identifier` to retrieve the `zaaktype`.
     *
     * @todo we cannot use the zaaktype URI to retrieve a zaaktype because it is bound to change when the zaaktype is updated. There doesn't seem to be a way to retrieve the zaaktype by identifier, so we have to get all the zaaktypen first and then filter them by identifier. We should change this when the API supports this.
     *
     * @see https://github.com/OpenWebconcept/plugin-owc-gravityforms-zaaksysteem/issues/13#issue-1697256063
     */
    public function getZaakType(string $zaaktypeIdentifier): ?Zaaktype
    {
        // Get the zaaktype belonging to the chosen zaaktype identifier.
        return $this->getApiClient()->zaaktypen()->all()->filter(
            function (Zaaktype $zaaktype) use ($zaaktypeIdentifier) {
                if ($zaaktype->identificatie === $zaaktypeIdentifier) {
                    return $zaaktype;
                }
            }
        )->first();
    }


    /**
     * Get the `zaakeigenschappen` belonging to the chosen `zaaktype`.
     */
    public function getZaaktypenEigenschappen(string $zaaktypeUrl): PagedCollection
    {
        $client = $this->getApiClient();

        $filter = new EigenschappenFilter();
        $filter->add('zaaktype', $zaaktypeUrl);

        return $client->eigenschappen()->filter($filter);
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

        if ($position !== 0 || empty($supplier)) {
            return;
        }

        // Get the selected zaaktype identifier from the form's settings.
        $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, $supplier)];

        // Get the zaaktype belonging to the chosen zaaktype identifier.
        $zaaktype = $this->getZaakType($zaaktypeIdentifier);

        if (empty($zaaktype['url'])) {
            $properties = [];
        } else {
            $properties = $this->getZaaktypenEigenschappen($zaaktype->url);
        }

        $options = [];
        if (!empty($properties)) {
            foreach ($properties as $property) {
                $options[] = [
                    'label' => $property['naam'],
                    'value' => $property['url']
                ];
            }
        }

        echo view('partials/gf-field-options.php', [
            'properties' => $options
        ]);
    }
}
