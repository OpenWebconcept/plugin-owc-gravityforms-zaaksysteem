<?php

namespace OWC\Zaaksysteem\GravityForms;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
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

        $zaaktypen = $this->getTypes($client);
        $types = $this->prepareTypes($zaaktypen);

        set_transient($transientKey, $types, 500);

        return $types;
    }

    protected function getTypes(Client $client): array
    {
        $page = 1;
        $zaaktypen = [];
        $requestException = '';

        while ($page) {
            try {
                $result = $client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
            } catch (Exception $e) {
                $requestException = $e->getMessage();

                break;
            }

            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        if (empty($zaaktypen)) {
            $exceptionMessage = 'No zaaktypen found.';

            if (! empty($requestException)) {
                $exceptionMessage = sprintf('%s %s', $exceptionMessage, $requestException);
            }

            throw new Exception($exceptionMessage);
        }

        return $zaaktypen;
    }

    protected function prepareTypes(array $zaaktypen): array
    {
        return (array) Collection::collect($zaaktypen)->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie,
            ];
        })->all();
    }

    protected function handleNoChoices(): array
    {
        return [
            [
                'label' => __('Unable to retrieve "Zaak" types provided by selected supplier.', 'owc-gravityforms-zaaksysteem'),
            ],
        ];
    }

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
                // TODO: verify if there is a way to actively get the selected value without a save and without custom JS.
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
                    'choices' => $this->getTypesOpenZaak(),
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
                    'choices' => $this->getTypesDecosJoin(),
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
                    'choices' => $this->getTypesRxMission(),
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
                    'choices' => $this->getTypesXxllnc(),
                ],
            ],
        ];

        return $fields;
    }
}
