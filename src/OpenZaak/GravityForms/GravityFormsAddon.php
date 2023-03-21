<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use GFAddOn;

use function OWC\OpenZaak\Foundation\Helpers\config;

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
    private string $prefix = 'owc-openzaak-';

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
            $this->settingsMaykin(),
            $this->settingsDecos(),
            $this->RSIN()
        ];
    }

    protected function settingsGeneral(): array
    {
        return [
            'title'  => esc_html__('Description', config('core.text_domain')),
            'description' => esc_html__(' Enter the details of the suppliers you would like to use.', config('core.text_domain')),
            'fields' => [[]],
        ];
    }

    protected function settingsMaykin(): array
    {
        return [
            'title'  => esc_html__('Maykin Media', config('core.text_domain')),
            'fields' => [
                [
                    'label'             => esc_html__('Base URL', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->prefix}maykin-url",
                ],
                [
                    'label'             => esc_html__('Client ID', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->prefix}maykin-client-id",
                ],
                [
                    'label'             => esc_html__('Client Secret', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->prefix}maykin-client-secret",
                ]
            ],
        ];
    }

    protected function settingsDecos(): array
    {
        return [

                'title'  => esc_html__('Decos JOIN', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Base URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}decos-url",
                    ],
                    [
                        'label'             => esc_html__('Token URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}decos-token-url",
                    ],
                    [
                        'label'             => esc_html__('Client ID', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}decos-client-id",
                    ],
                    [
                        'label'             => esc_html__('Client Secret', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}decos-client-secret",
                    ]
                ],


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
                    'name'              => "{$this->prefix}rsin",
                    'description'       => 'Registration number for non-natural persons, also known as the counterpart of the citizen service number (BSN).'
                ]
            ],
        ];
    }
}
