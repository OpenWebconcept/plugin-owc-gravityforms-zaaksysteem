<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Http\RequestError;

use function OWC\Zaaksysteem\Foundation\Helpers\config;

class GravityFormsFormSettings
{
    protected string $prefix = OWC_GZ_PLUGIN_SLUG;

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
    protected function getApiClient(string $client): Client
    {
        switch ($client) {
            case 'decos':
                return $this->plugin->getContainer()->get('dj.client');
            default:
                return $this->plugin->getContainer()->get('oz.client');
        }
    }

    /**
     * Get the URL of the catalogi endpoint of the selected client.
     */
    protected function getCatalogiURL(string $client): string
    {
        switch ($client) {
            case 'decos':
                return $this->plugin->getContainer()->get('dj.catalogi_url');
            default:
                return $this->plugin->getContainer()->get('oz.catalogi_url');
        }
    }

    /**
     * Get a list of related 'zaaktypen' from Open Zaak.
     */
    public function getTypesOpenZaak(): array
    {
        $client = $this->getApiClient('openzaak');
        $client->setEndpointURL($this->getCatalogiURL('openzaak'));

        try {
            $zaaktypen = $client->zaaktypen()->all();
        } catch(RequestError $exception) {
            //REFERENCE POINT: Mike -> just return, let it break or catch and log?
            return [];
        }

        return $zaaktypen->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie
            ];
        })->all();
    }

    /**
     * Get a list of related 'zaaktypen' from Decos Join.
     */
    public function getTypesDecosJoin(): array
    {
        $client = $this->getApiClient('decos');
        $client->setEndpointURL($this->getCatalogiURL('decos'));

        try {
            $zaaktypen = $client->zaaktypen()->all();
        } catch(RequestError $exception) {
            // REFERENCE POINT: Mike -> just return, let it break or catch and log?
            return [];
        }

        return $zaaktypen->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie
            ];
        })->all();
    }

    /**
     * Add form based settings.
     */
    public function addFormSettings(array $fields): array
    {
        $fields[] = [
            'title'  => esc_html__('Zaaksysteem', config('core.text_domain')),
            'fields' => [
                [
                    'name'    => "{$this->prefix}-form-setting-supplier",
                    'default_value' => "{$this->prefix}-form-setting-supplier-none",
                    'tooltip' => '<h6>' . __('Select a supplier', config('core.text_domain')) . '</h6>' . __('Choose the Zaaksysteem supplier. Please note that you\'ll also need to configure the credentials in the Gravity Forms main settings.', config('core.text_domain')),
                    'type'    => 'select',
                    'label'   => esc_html__('Select a supplier', config('core.text_domain')),
                    'choices' => [
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-none",
                            'label' => __('Select supplier', config('core.text_domain')),
                            'value' => 'none',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-openzaak",
                            'label' => __('OpenZaak', config('core.text_domain')),
                            'value' => 'openzaak',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-decos-join",
                            'label' => __('Decos Join', config('core.text_domain')),
                            'value' => 'decos-join',
                        ],
                    ],
                ],
                // TODO: verify if there is a way to actively get the selected value without a save and without custom JS.
                [
                    'name'    => "{$this->prefix}-form-setting-openzaak-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('OpenZaak identifier', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['openzaak'],
                            ],
                        ],
                    ],
                    'choices' => $this->getTypesOpenZaak(),
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-decos-join-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Decos Join identifier', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['decos-join'],
                            ],
                        ],
                    ],
                    'choices' => $this->getTypesDecosJoin(),
                ]
            ],
        ];

        return $fields;
    }
}
