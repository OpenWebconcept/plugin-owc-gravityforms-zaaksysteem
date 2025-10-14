<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\DecosClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\MozartClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\OpenWaveClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\OpenZaakClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\ProcuraClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\RxMissionClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\XxllncClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Services\TypeRetrievalService;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Support\TypeCache;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class GravityFormsFormSettings
{
    protected string $prefix = OWC_GZ_PLUGIN_SLUG;

    /**
     * Add form based settings.
     */
    public function addFormSettings(array $fields, array $form): array
    {
        $supplierChoices = [
            [
                'name' => "{$this->prefix}-form-setting-supplier-none",
                'label' => __('Selecteer leverancier', 'owc-gravityforms-zaaksysteem'),
                'value' => 'none',
            ],
        ];
        if (ContainerResolver::make()->get('dj.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-decos-join",
                'label' => __('Decos Join', 'owc-gravityforms-zaaksysteem'),
                'value' => 'decos-join',
            ];
        }
        if (ContainerResolver::make()->get('mz.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-mozart",
                'label' => __('Mozart', 'owc-gravityforms-zaaksysteem'),
                'value' => 'mozart',
            ];
        }
        if (ContainerResolver::make()->get('ow.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-openwave",
                'label' => __('OpenWave', 'owc-gravityforms-zaaksysteem'),
                'value' => 'openwave',
            ];
        }
        if (ContainerResolver::make()->get('oz.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-openzaak",
                'label' => __('OpenZaak', 'owc-gravityforms-zaaksysteem'),
                'value' => 'openzaak',
            ];
        }
        if (ContainerResolver::make()->get('procura.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-procura",
                'label' => __('Shift2', 'owc-gravityforms-zaaksysteem'),
                'value' => 'procura',
            ];
        }
        if (ContainerResolver::make()->get('rx.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-rx-mission",
                'label' => __('Rx.Mission', 'owc-gravityforms-zaaksysteem'),
                'value' => 'rx-mission',
            ];
        }
        if (ContainerResolver::make()->get('xxllnc.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-xxllnc",
                'label' => __('Xxllnc', 'owc-gravityforms-zaaksysteem'),
                'value' => 'xxllnc',
            ];
        }

        $fields['owc-gravityforms-zaaksysteem'] = [
            'title' => esc_html__('Zaaksysteem', 'owc-gravityforms-zaaksysteem'),
            'description' => esc_html__('Om de snelheid te verhogen worden de instellingen van leveranciers pas opgehaald na het kiezen van een leverancier. Dit betekent dat de pagina herladen moet worden na het selecteren van een leverancier.', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'name' => "{$this->prefix}-form-setting-supplier",
                    'default_value' => "{$this->prefix}-form-setting-supplier-none",
                    'tooltip' => '<h6>' . __('Selecteer een leverancier', 'owc-gravityforms-zaaksysteem') . '</h6>' . __('Kies een Zaaksysteem leverancier. Let op dat je ook de instellingen van de leverancier moet configureren in de hoofdinstellingen van Gravity Forms.', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'select',
                    'label' => esc_html__('Selecteer een leverancier', 'owc-gravityforms-zaaksysteem'),
                    'choices' => $supplierChoices,
                ],
                [
                    'name' => "{$this->prefix}-form-setting-supplier-manually",
                    'default_value' => "1",
                    'tooltip' => '<h6>' . __('Leverancier instellingen', 'owc-gravityforms-zaaksysteem') . '</h6>' . __('Kies hoe de leverancier instellingen geconfigureerd moeten worden.', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'radio',
                    'label' => esc_html__('Leverancier instellingen', 'owc-gravityforms-zaaksysteem'),
                    'choices' => [
                        [
                            'name' => "{$this->prefix}-form-setting-supplier-manually-disabled",
                            'label' => __('Selecteer instellingen (opgehaald vanuit zaaksysteem)', 'owc-gravityforms-zaaksysteem'),
                            'value' => '0',
                        ],
                        [
                            'name' => "{$this->prefix}-form-setting-supplier-manually-enabled",
                            'label' => __('Configureer instellingen handmatig (invoeren van URL\'s)', 'owc-gravityforms-zaaksysteem'),
                            'value' => '1',
                        ],
                    ],
                ],
            ],
        ];

        $fields['owc-gravityforms-zaaksysteem']['fields'] = $this->getFieldsBySupplier($form, $fields['owc-gravityforms-zaaksysteem']['fields']);

        return $fields;
    }

    /**
     * Retrieves the fields associated with a specific supplier based on the form settings and merge with existing fields.
     */
    protected function getFieldsBySupplier(array $form, array $fields): array
    {
        $supplierSetting = $form["{$this->prefix}-form-setting-supplier"] ?? '';
        $manual = $form["{$this->prefix}-form-setting-supplier-manually"] ?? '0';
        $suppliersFields = $this->fieldsBySupplier($form);

        if (empty($supplierSetting) || empty($suppliersFields[$supplierSetting][$manual ? 'manual_setting' : 'select_setting'])) {
            return $fields;
        }

        return array_merge($fields, $suppliersFields[$supplierSetting][$manual ? 'manual_setting' : 'select_setting']);
    }

    /**
     * Fields associated with suppliers, used for matching the fields of the selected supplier in form settings.
     * This approach minimizes unnecessary requests to multiple sources that are not needed. Because only one supplier can be selected.
     */
    protected function fieldsBySupplier($form): array
    {
        $fields = [];

        $decosClient = ContainerResolver::make()->getApiClient('decos-join');
        $mozartClient = ContainerResolver::make()->getApiClient('mozart');
        $openWaveClient = ContainerResolver::make()->getApiClient('openwave');
        $openZaakClient = ContainerResolver::make()->getApiClient('openzaak');
        $procuraClient = ContainerResolver::make()->getApiClient('procura');
        $rxMissionClient = ContainerResolver::make()->getApiClient('rx-mission');

        if (ContainerResolver::make()->get('dj.enabled') && $this->supplierIsSelectedInFormSettings($form, 'decos-join')) {
            $decosAdapterClient = new DecosClient($decosClient->getClientNamePretty(), new TypeRetrievalService($decosClient), new TypeCache());

            $fields['decos-join'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $decosAdapterClient->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $decosAdapterClient->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('mz.enabled') && $this->supplierIsSelectedInFormSettings($form, 'mozart')) {
            $mozartAdapterClient = new MozartClient($mozartClient->getClientNamePretty(), new TypeRetrievalService($mozartClient), new TypeCache());

            $fields['mozart'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-mozart-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['mozart'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $mozartAdapterClient->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-mozart-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['mozart'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $mozartAdapterClient->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-mozart-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['mozart'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-mozart-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['mozart'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('ow.enabled') && $this->supplierIsSelectedInFormSettings($form, 'openwave')) {
            $openWaveAdapterClient = new OpenWaveClient($openWaveClient->getClientNamePretty(), new TypeRetrievalService($openWaveClient), new TypeCache());
            $fields['openwave'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-openwave-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openwave'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $openWaveAdapterClient->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-openwave-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openwave'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $openWaveAdapterClient->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-openwave-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openwave'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-openwave-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openwave'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('oz.enabled') && $this->supplierIsSelectedInFormSettings($form, 'openzaak')) {
            $openZaakAdapterClient = new OpenZaakClient($openZaakClient->getClientNamePretty(), new TypeRetrievalService($openZaakClient), new TypeCache());

            $fields['openzaak'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $openZaakAdapterClient->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $openZaakAdapterClient->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('procura.enabled') && $this->supplierIsSelectedInFormSettings($form, 'procura')) {
            $procuraAdapterClient = new ProcuraClient($procuraClient->getClientNamePretty(), new TypeRetrievalService($procuraClient), new TypeCache());

            $fields['procura'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-procura-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $procuraAdapterClient->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-procura-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $procuraAdapterClient->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-procura-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-procura-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('rx.enabled') && $this->supplierIsSelectedInFormSettings($form, 'rx-mission')) {
            $rxMissionAdapterClient = new RxMissionClient($rxMissionClient->getClientNamePretty(), new TypeRetrievalService($form), new TypeCache());
            $fields['rx-mission'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $rxMissionAdapterClient->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => $rxMissionAdapterClient->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('xxllnc.enabled') && $this->supplierIsSelectedInFormSettings($form, 'xxllnc')) {
            $fields['xxllnc'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new XxllncClient(ContainerResolver::make()->getApiClient('xxllnc')->getClientNamePretty(), new TypeRetrievalService(ContainerResolver::make()->getApiClient('xxllnc')), new TypeCache()))->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new XxllncClient(ContainerResolver::make()->getApiClient('xxllnc')->getClientNamePretty(), new TypeRetrievalService(ContainerResolver::make()->getApiClient('xxllnc')), new TypeCache()))->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $fields;
    }

    private function supplierIsSelectedInFormSettings(array $form, string $supplier): bool
    {
        $supplierSetting = (string) ($form["{$this->prefix}-form-setting-supplier"] ?? '');

        return $supplierSetting === $supplier ? true : false;
    }
}
