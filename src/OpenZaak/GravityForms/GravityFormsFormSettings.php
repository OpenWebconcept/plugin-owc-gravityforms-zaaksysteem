<?php

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\config;

class GravityFormsFormSettings
{
    protected string $prefix = 'owc-openzaak-';

    public function addFormSettings(array $fields): array
    {
        $fields[] = [
            'title'  => esc_html__('OpenZaak', config('core.text_domain')),
            'fields' => [
                [
                    'name'    => "{$this->prefix}gf-form-setting-supplier",
                    'tooltip' => '<h6>' . __('Select a supplier', config('core.text_domain')) . '</h6>' . __('Choose the OpenZaak supplier. Please note that you\'ll also need to configure the credentials in the Gravity Forms main settings.', config('core.text_domain')),
                    'type'    => 'select',
                    'label'   => esc_html__('Select a supplier', config('core.text_domain')),
                    'choices' => [
                        [
                            'name'  => "{$this->prefix}gf-form-setting-supplier-none",
                            'label' => __('Select supplier', config('core.text_domain')),
                            'value' => 'none',
                        ],
                        [
                            'name'  => "{$this->prefix}gf-form-setting-supplier-maykin",
                            'label' => __('Maykin Media', config('core.text_domain')),
                            'value' => 'maykin-media',
                        ],
                        [
                            'name'  => "{$this->prefix}gf-form-setting-supplier-decos",
                            'label' => __('Decos', config('core.text_domain')),
                            'value' => 'decos',
                        ],
                    ],
                ],
            ],
        ];

        return $fields;
    }
}
