<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Logger;

use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\GravityForms\GravityFormsSettings;

class LoggerServiceProvider extends ServiceProvider
{
    protected string $settingsPrefix = OWC_GZ_PLUGIN_SLUG;

    public function boot(): void
    {
        $this->setLoggingLevel();

        if ($this->isEnabled()) {
            add_action('http_api_debug', [$this, 'logHttpMessage'], 10, 5);
        }

        add_filter('owc_gravityforms_zaaksysteem_gf_settings', [$this, 'addLoggingOptions']);
    }

    public function isEnabled(): bool
    {
        $container = $this->plugin->getContainer();

        return (bool) $container->get('message.logger.active');
    }

    public function logHttpMessage($response, $context, $class, $arguments, $url)
    {
        if (! ($arguments['headers']['_owc_request_logging'] ?? false)) {
            return;
        }

        $this->plugin->getContainer()->get('message.logger')->debug($url, compact('arguments', 'response'));
    }

    public function addLoggingOptions(array $fields): array
    {
        // Insert logging fields after the 'Algemene instellingen' fields.
        $columns = array_column($fields, 'title');
        $index = (array_flip($columns)['Algemene instellingen'] ?? 0) + 1;

        return array_merge(
            array_slice($fields, 0, $index),
            [$this->getFieldSettings()],
            array_slice($fields, $index, count($fields))
        );
    }

    protected function getFieldSettings(): array
    {
        return [
            'title'  => esc_html__('Berichtenverkeer logboek', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'name'    => "{$this->settingsPrefix}-form-setting-logging",
                    'default_value' => "0",
                    'tooltip' => sprintf(
                        '<h6>%s</h6>%s',
                        __('Berichtenverkeer logboek', 'owc-gravityforms-zaaksysteem'),
                        __('Activeer het loggen van het HTTP berichtenverkeer. Afhankelijk van het logniveau worden er meer of minder details bijgehouden.', 'owc-gravityforms-zaaksysteem')
                    ),
                    'type'    => 'select',
                    'label'   => esc_html__('Selecteer logboek granulariteit', 'owc-gravityforms-zaaksysteem'),
                    'choices' => [
                        [
                            'name'  => "{$this->settingsPrefix}-form-setting-logging-none",
                            'label' => __('Deactiveer logboek', 'owc-gravityforms-zaaksysteem'),
                            'value' => '0',
                        ],
                        [
                            'name'  => "{$this->settingsPrefix}-form-setting-logging-" . MessageDetail::WHITE_BOX,
                            'label' => __('Hoog (White box)', 'owc-gravityforms-zaaksysteem'),
                            'value' => MessageDetail::WHITE_BOX,
                        ],
                        [
                            'name'  => "{$this->settingsPrefix}-form-setting-logging-" . MessageDetail::GRAY_BOX,
                            'label' => __('Gemiddeld (Gray box)', 'owc-gravityforms-zaaksysteem'),
                            'value' => MessageDetail::GRAY_BOX,
                        ],
                        [
                            'name'  => "{$this->settingsPrefix}-form-setting-logging-" . MessageDetail::BLACK_BOX,
                            'label' => __('Laag (Black box)', 'owc-gravityforms-zaaksysteem'),
                            'value' => MessageDetail::BLACK_BOX,
                        ],
                        [
                            'name'  => "{$this->settingsPrefix}-form-setting-logging-" . MessageDetail::URL_LOGGING,
                            'label' => __('Enkel URLs', 'owc-gravityforms-zaaksysteem'),
                            'value' => MessageDetail::URL_LOGGING,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function setLoggingLevel(): void
    {
        $container = $this->plugin->getContainer();
        $configured = GravityFormsSettings::make()->get('-form-setting-logging') ?: '0';

        switch ($configured) {
            case MessageDetail::BLACK_BOX:
            case MessageDetail::GRAY_BOX:
            case MessageDetail::WHITE_BOX:
            case MessageDetail::URL_LOGGING:
                $container->set('message.logger.detail', $configured);
                $container->set('message.logger.active', true);
                break;
            case '0':
            default:
                $container->set('message.logger.active', false);
                break;
        }
    }
}
