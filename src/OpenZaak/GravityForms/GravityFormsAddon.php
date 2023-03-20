<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use GFAddOn;

use function OWC\OpenZaak\Foundation\Helpers\config;
use function OWC\OpenZaak\Foundation\Helpers\get_supplier;

class GravityFormsAddon extends GFAddOn
{
    /**
     * Version number.
     */
    protected $_version = OZ_VERSION;

    /**
     * Minimal required GF version.
     */
    protected $_min_gravityforms_version = '2.4';

    /**
     * Subview slug.
     *
     * @var string
     */
    protected $_slug = OZ_PLUGIN_SLUG;

    /**
     * Relative path to the plugin from the plugins folder.
     */
    protected $_path = OZ_ROOT_PATH . '/plugin.php';

    /**
     * The physical path to the main plugin file.
     */
    protected $_full_path = __FILE__;

    /**
     * The complete title of the Add-On.
     */
    protected $_title = 'OWC OpenZaak';

    /**
     * The short title of the Add-On to be used in limited spaces.
     */
    protected $_short_title = 'OWC OpenZaak';

    /**
     * Field prefix in Gravity Forms.
     */
    private string $settingsPrefix = 'owc-openzaak-';

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
        $settings = sprintf('%sSettings', get_supplier()); // Compose method name based on supplier name.

        return $this->$settings();
    }

    protected function MaykinMediaSettings(): array
    {
        return [
            [
                'title'  => esc_html__('Settings', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Base URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}maykin-url",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client ID', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}maykin-client-id",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client Secret', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}maykin-client-secret",
                        'required'          => true
                    ]
                ],
            ],
        ];
    }

    protected function DecosSettings(): array
    {
        return [
            [
                'title'  => esc_html__('Settings', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Base URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}decos-url",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Token URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}decos-token-url",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client ID', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}decos-client-id",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client Secret', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->settingsPrefix}decos-client-secret",
                        'required'          => true
                    ]
                ],
            ],
            $this->RSIN()
        ];
    }

    protected function RSIN(): array
    {
        return [
            'title'  => esc_html__('Organization', config('core.text_domain')),
            'fields' => [
                [
                    'label'             => esc_html__('RSIN', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->settingsPrefix}rsin",
                    'required'          => true,
                    'description'       => 'Registration number for non-natural persons, also known as the counterpart of the citizen service number (BSN).'
                ]
            ],
        ];
    }
}
