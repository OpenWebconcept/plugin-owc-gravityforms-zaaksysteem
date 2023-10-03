<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use GFAddOn;

use function OWC\Zaaksysteem\Foundation\Helpers\config;

class GravityFormsAddon extends GFAddOn
{
    /**
     * Version number.
     */
    protected $_version = OWC_GZ_VERSION;

    /**
     * Minimal required GF version.
     */
    protected $_min_gravityforms_version = '2.4';

    /**
     * Subview slug.
     *
     * @var string
     */
    protected $_slug = OWC_GZ_PLUGIN_SLUG;

    /**
     * Relative path to the plugin from the plugins folder.
     */
    protected $_path = OWC_GZ_ROOT_PATH . '/owc-gravityforms-zaaksysteem.php';

    /**
     * The physical path to the main plugin file.
     */
    protected $_full_path = __FILE__;

    /**
     * The complete title of the Add-On.
     */
    protected $_title = OWC_GZ_NAME;

    /**
     * The short title of the Add-On to be used in limited spaces.
     */
    protected $_short_title = OWC_GZ_SHORT_NAME;

    /**
     * Field prefix in Gravity Forms.
     */
    private string $prefix = OWC_GZ_PLUGIN_SLUG;

    /**
     * Instance object
     *
     * @var self
     */
    private static $_instance = null;

    /**
     * Singleton loader.
     */
    public static function get_instance(): self
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
     *
     * @return array
     */
    public function plugin_settings_fields()
    {
        return [
            $this->settingsGeneral(),
            $this->settingsOpenZaak(),
            $this->settingsDecosJoin(),
            $this->settingsRxMission(),
            $this->RSIN()
        ];
    }

    protected function settingsGeneral(): array
    {
        return [
            'title' => esc_html__('Description', config('core.text_domain')),
            'description' => esc_html__('Enter the details of the suppliers you would like to use. When configuring the API endpoints don\'t add the endpoint type, e.g. "https://supplier.com/api/v1/eigenschappen" should be "https://supplier.com/api/v1".', config('core.text_domain')),
            'fields' => [[]],
        ];
    }

    protected function settingsOpenZaak(): array
    {
        return [
            'title'  => esc_html__('OpenZaak', config('core.text_domain')),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-client-secret",
                ]
            ],
        ];
    }

    protected function settingsDecosJoin(): array
    {
        return [
            'title'  => esc_html__('Decos Join', config('core.text_domain')),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-client-secret",
                ],
                [
                    'label' => esc_html__('Client Secret (ZRC)', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-client-secret-zrc",
                ],
            ],
        ];
    }

    protected function settingsRxMission(): array
    {
        return [
            'title'  => esc_html__('Rx.Mission', config('core.text_domain')),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-client-secret",
                ]
            ],
        ];
    }

    protected function RSIN(): array
    {
        return [
            'title' => esc_html__('Organization', config('core.text_domain')),
            'fields' => [
                [
                    'label' => esc_html__('RSIN', config('core.text_domain')),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rsin",
                    'description' => 'Registration number for non-natural persons, also known as the counterpart of the citizen service number (BSN).'
                ]
            ],
        ];
    }
}
