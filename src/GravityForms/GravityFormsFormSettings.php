<?php

namespace OWC\Zaaksysteem\GravityForms;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use function OWC\Zaaksysteem\Foundation\Helpers\config;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

use OWC\Zaaksysteem\Support\Collection;

use function Yard\ConfigExpander\Foundation\Helpers\value;

class GravityFormsFormSettings
{
    protected string $prefix = OWC_GZ_PLUGIN_SLUG;

    /**
     * Get a list of related 'zaaktypen' from Open Zaak.
     */
    public function getTypesOpenZaak(): array
    {
        try {
            return $this->getTypesByClient(ContainerResolver::make()->getApiClient('openzaak'));
        } catch(Exception $e) {
            return $this->handleNoChoices();
        }
    }

    /**
     * Get a list of related 'zaaktypen' from Decos Join.
     */
    public function getTypesDecosJoin(): array
    {
        try {
            return $this->getTypesByClient(ContainerResolver::make()->getApiClient('decos'));
        } catch(Exception $e) {
            return $this->handleNoChoices();
        }
    }

    /**
     * Get a list of related 'zaaktypen' from Rx.Mission.
     */
    public function getTypesRxMission(): array
    {
        try {
            return $this->getTypesByClient(ContainerResolver::make()->getApiClient('rx-mission'));
        } catch(Exception $e) {
            return $this->handleNoChoices();
        }
    }

    /**
     * Get a list of related 'zaaktypen' from Rx.Mission.
     */
    public function getTypesXxllnc(): array
    {
        try {
            return $this->getTypesByClient(ContainerResolver::make()->getApiClient('xxllnc'));
        } catch(Exception $e) {
            return $this->handleNoChoices();
        }
    }

    /**
     * Return types by client, includes pagination.
     */
    protected function getTypesByClient(Client $client): array
    {
        $transientKey = sprintf('%s-form-settings-zaaktypen', $client->getClientNamePretty());
        $types = get_transient($transientKey);

        if (is_array($types) && $types) {
            return $types;
        }

        $page = 1;
        $zaaktypen = [];

        while ($page) {
            $result = $client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        $types = (array) Collection::collect($zaaktypen)->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie,
            ];
        })->all();

        set_transient($transientKey, $types, 500);

        return $types;
    }

    protected function handleNoChoices(): array
    {
        return [
            [
                'label' => __('Unable to retrieve "Zaak" types provided by selected supplier.', config('core.text_domain')),
            ],
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
                            'name'  => "{$this->prefix}-form-setting-supplier-openzaak",
                            'label' => __('OpenZaak', config('core.text_domain')),
                            'value' => 'openzaak',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-decos-join",
                            'label' => __('Decos Join', config('core.text_domain')),
                            'value' => 'decos-join',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-rx-mission",
                            'label' => __('Rx.Mission', config('core.text_domain')),
                            'value' => 'rx-mission',
                        ],
                        [
                            'name'  => "{$this->prefix}-form-setting-supplier-xxllnc",
                            'label' => __('Xxllnc', config('core.text_domain')),
                            'value' => 'xxllnc',
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
                    'name'    => "{$this->prefix}-form-setting-rx-mission-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Rx.Mission identifier', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['rx-mission'],
                            ],
                        ],
                    ],
                    'choices' => $this->getTypesRxMission(),
                ],
                [
                    'name'    => "{$this->prefix}-form-setting-xxllnc-identifier",
                    'type'    => 'select',
                    'label'   => esc_html__('Xxllnc identifier', config('core.text_domain')),
                    'dependency' => [
                        'live'   => true,
                        'fields' => [
                            [
                                'field' => "{$this->prefix}-form-setting-supplier",
                                'values' => ['xxllnc'],
                            ],
                        ],
                    ],
                    'choices' => $this->getTypesXxllnc(),
                ],
            ],
        ];

        return $fields;
    }
}
