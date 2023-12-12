<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\DecosClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\OpenZaakClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\RxMissionClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\XxllncClient;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class GravityFormsFormSettings
{
    protected string $prefix = OWC_GZ_PLUGIN_SLUG;

    /**
     * Add form based settings.
     */
    public function addFormSettings(array $fields): array
    {
        $fields[] = [
            'title'  => esc_html__('Zaaksysteem', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'name'    => "{$this->prefix}-form-setting-supplier",
                    'default_value' => "{$this->prefix}-form-setting-supplier-none",
                    'tooltip' => '<h6>' . __('Select a supplier', 'owc-gravityforms-zaaksysteem') . '</h6>' . __('Choose the Zaaksysteem supplier. Please note that you\'ll also need to configure the credentials in the Gravity Forms main settings.', 'owc-gravityforms-zaaksysteem'),
                    'type'    => 'select',
                    'label'   => esc_html__('Select a supplier', 'owc-gravityforms-zaaksysteem'),
                    'choices' => [
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-none",
                            'label' => __('Select supplier', 'owc-gravityforms-zaaksysteem'),
                            'value' => 'none',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-openzaak",
                            'label' => __('OpenZaak', 'owc-gravityforms-zaaksysteem'),
                            'value' => 'openzaak',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-decos-join",
                            'label' => __('Decos Join', 'owc-gravityforms-zaaksysteem'),
                            'value' => 'decos-join',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-rx-mission",
                            'label' => __('Rx.Mission', 'owc-gravityforms-zaaksysteem'),
                            'value' => 'rx-mission',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-xxllnc",
                            'label' => __('Xxllnc', 'owc-gravityforms-zaaksysteem'),
                            'value' => 'xxllnc',
                        ],
                    ],
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-openzaak-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('OpenZaak identifier', 'owc-gravityforms-zaaksysteem'),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['openzaak'],
                            ],
                        ],
                    ],
                    'choices' => (new OpenZaakClient(ContainerResolver::make()->getApiClient('openzaak')))->zaaktypen(),
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-decos-join-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Decos Join identifier', 'owc-gravityforms-zaaksysteem'),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['decos-join'],
                            ],
                        ],
                    ],
                    'choices' => (new DecosClient(ContainerResolver::make()->getApiClient('decos')))->zaaktypen(),
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-rx-mission-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Rx.Mission identifier', 'owc-gravityforms-zaaksysteem'),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['rx-mission'],
                            ],
                        ],
                    ],
                    'choices' => (new RxMissionClient(ContainerResolver::make()->getApiClient('rx-mission')))->zaaktypen(),
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-xxllnc-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Xxllnc identifier', 'owc-gravityforms-zaaksysteem'),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['xxllnc'],
                            ],
                        ],
                    ],
                    'choices' => (new XxllncClient(ContainerResolver::make()->getApiClient('xxllnc')))->zaaktypen(),
                ],
            ],
        ];

        return $fields;
    }
}
