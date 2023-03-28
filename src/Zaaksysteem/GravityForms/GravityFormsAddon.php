<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use function OWC\Zaaksysteem\Foundation\Helpers\config;

use GFAddOn;

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
    protected $_path = OZ_ROOT_PATH . '/owc-gravityforms-zaaksysteem.php';

    /**
     * The physical path to the main plugin file.
     */
    protected $_full_path = __FILE__;

    /**
     * The complete title of the Add-On.
     */
    protected $_title = OZ_NAME;

    /**
     * The short title of the Add-On to be used in limited spaces.
     */
    protected $_short_title = OZ_SHORT_NAME;

    /**
     * Field prefix in Gravity Forms.
     */
    private string $prefix = OZ_PLUGIN_SLUG;

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
            $this->settingsEnableU(),
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

    protected function settingsOpenZaak(): array
    {
        return [
            'title'  => esc_html__('OpenZaak', config('core.text_domain')),
            'fields' => [
                [
                    'label'             => esc_html__('Base URL', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->prefix}-openzaak-url",
                ],
                [
                    'label'             => esc_html__('Client ID', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->prefix}-openzaak-client-id",
                ],
                [
                    'label'             => esc_html__('Client Secret', config('core.text_domain')),
                    'type'              => 'text',
                    'class'             => 'medium',
                    'name'              => "{$this->prefix}-openzaak-client-secret",
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
                        'label'             => esc_html__('Base URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}-decos-join-url",
                    ],
                    [
                        'label'             => esc_html__('Token URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}-decos-join-token-url",
                    ],
                    [
                        'label'             => esc_html__('Client ID', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}-decos-join-client-id",
                    ],
                    [
                        'label'             => esc_html__('Client Secret', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}-decos-join-client-secret",
                    ]
                ],


        ];
    }

    protected function settingsEnableU(): array
    {
        return [
                'title'  => esc_html__('Enabe U', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Base URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$this->prefix}-enable-u-url",
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
                    'name'              => "{$this->prefix}-rsin",
                    'description'       => 'Registration number for non-natural persons, also known as the counterpart of the citizen service number (BSN).'
                ]
            ],
        ];
    }
}
