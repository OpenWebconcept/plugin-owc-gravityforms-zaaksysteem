<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Foundation\Plugin;

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
     */
    protected function getApiClient(string $client): Client
    {
        switch ($client) {
            case 'openzaak': //fallthrough
            case 'roxit':
                return $this->plugin->getContainer()->get('ro.client');
            default:
                return $this->plugin->getContainer()->get('oz.client');
        }
    }

    /**
     * Get a list of related 'zaaktypen' from Open Zaak.
     */
    public function getTypesOpenZaak(): array
    {
        $client = $this->getApiClient('openzaak');

        return $client->zaaktypen()->all()->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie
            ];
        })->all();
    }

    /**
     * Get a list of related 'zaaktypen' from Roxit.
     */
    public function getTypesRoxit(): array
    {
        $client = $this->getApiClient('roxit');

        return $client->zaaktypen()->all()->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie
            ];
        })->all();
    }

    /**
     * Get a list of related 'zaaktypen' from Decos Join.
     *
     * TODO: implement api
     */
    public function getTypesDecosJoin(): array
    {
        return [
            [
                'name' => 'Todo',
                'label' => 'Todo',
            ]
        ];
    }

    /**
     * Get a list of related 'zaaktypen' from Enable U.
     *
     * TODO: endpoint for retrieving types is not production ready.
     */
    public function getTypesEnableU(): array
    {
        return [
            [
                'name' => 'Todo',
                'label' => 'Todo',
            ]
        ];
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
                            'name'  => "{$this->prefix}-form-setting-supplier-decos-join",
                            'label' => __('Decos Join', config('core.text_domain')),
                            'value' => 'decos-join',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-enable-u",
                            'label' => __('EnableU', config('core.text_domain')),
                            'value' => 'enable-u',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-openzaak",
                            'label' => __('OpenZaak', config('core.text_domain')),
                            'value' => 'openzaak',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-roxit",
                            'label' => __('Rx.Services Roxit', config('core.text_domain')),
                            'value' => 'roxit',
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
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-enable-u-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('EnableU identifier', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['enable-u'],
                            ],
                        ],
                    ],
                    'choices' => $this->getTypesEnableU(),
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-roxit-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Roxit identifier', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['roxit'],
                            ],
                        ],
                    ],
                    'choices' => $this->getTypesRoxit(),
                ]
            ],
        ];

        return $fields;
    }
}
