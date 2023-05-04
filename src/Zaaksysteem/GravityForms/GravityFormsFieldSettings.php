<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Endpoint\Filter\EigenschappenFilter;
use OWC\Zaaksysteem\Foundation\Plugin;

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
        return $this->plugin->getContainer()->get('oz.client');
    }

    /**
     * Get the `zaakeigenschappen` belonging to the chosen `zaaktype`.
     */
    public function getZaaktypenEigenschappen(string $zaakTypeUrl): array
    {
        $client = $this->getApiClient();

        $filter = new EigenschappenFilter();
        $filter->get('zaaktype', $zaakTypeUrl);

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

        // Get the selected zaaktype from the form settings.
        $zaaktype = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, $supplier)];

        // Get the zaakeigenschappen belonging to the chosen zaaktype.
        $properties = $this->getZaaktypenEigenschappen($zaaktype);

        $options = [];
        foreach ($properties as $property) {
            $options[] = [
                'label' => $property['naam'],
                'value' => $property['url']
            ];
        }

        echo view('partials/gf-field-options.php', [
            'properties' => $options
        ]);
    }
}
