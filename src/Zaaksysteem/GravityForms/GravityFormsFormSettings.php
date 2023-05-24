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
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-enable-u-documenttype",
                    'type'    => 'select',
                    'label'   => esc_html__('EnableU Document Type', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['enable-u'],
                            ],
                        ],
                    ],
                    'choices' => $this->getDocumentTypesEnableU(),
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

    /**
     * Endpoint for retrieving document types is not available.
     * There return a hardcoded array
     */
    public function getDocumentTypesEnableU(): array
    {
        return [
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d870', 'label' =>  'Aanvraag eFormulier'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d871', 'label' =>  'Aanvraag - Situatietekening'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d872', 'label' =>  'Aanvraag - Verklaring'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d873', 'label' =>  'Aanvraag - Draaiboek'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d874', 'label' =>  'Aanvraag - Overig'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d875', 'label' =>  'Aanvraag - Facturen'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d876', 'label' =>  'Aanvraag - Fotoâ€™s'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d877', 'label' =>  'Zienswijze - bijlage'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d878', 'label' =>  'Melding - Situatietekening'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d879', 'label' =>  'Melding - Overig'],
            ['value' => '3beec26e-e43f-4fd2-ba09-94d47316d880', 'label' =>  'Aanvraag']
        ];

    }
}
