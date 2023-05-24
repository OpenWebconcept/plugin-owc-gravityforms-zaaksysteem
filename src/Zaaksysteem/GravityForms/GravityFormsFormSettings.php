<?php

namespace OWC\Zaaksysteem\GravityForms;

use function OWC\Zaaksysteem\Foundation\Helpers\config;

class GravityFormsFormSettings
{
    protected string $prefix = OZ_PLUGIN_SLUG;

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
                            'label' => __('EnableU', config('core.text_domain')),
                            'value' => 'enable-u',
                        ],
                    ],
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
                    'choices' => $this->getZaakTypesEnableU(),
                ]
            ]
        ];

        return $fields;
    }
    
    /**
     * Get a list of related 'zaaktypen' from Enable U.
     *
     * TODO: endpoint for retrieving types is not production ready.
     */
    public function getZaakTypesEnableU(): array
    {
        return [
            [
                'name' => 'Todo',
                'label' => 'Todo',
            ]
        ];
    }
}
