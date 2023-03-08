<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\config;
use GFAddOn;

class GravityFormsAddOnSettings extends GFAddOn
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
     */
    protected $_slug = 'owc-gravityforms-openzaak';

    /**
     * Relative path to the plugin from the plugins folder.

     */
    protected $_path = OZ_ROOT_PATH . '/owc-openzaak.php';

    /**
     * The physical path to the main plugin file.
     */
    protected $_full_path = __FILE__;

    /**
     * The complete title of the Add-On.
     */
    protected $_title = 'OWC GravityForms OpenZaak';

    /**
     * The short title of the Add-On to be used in limited spaces.

     */
    protected $_short_title = 'OWC OpenZaak';

    /**
     * Instance object
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
        $prefix = "owc-openzaak-";
        return [
            [
                'title'  => esc_html__('Settings', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Base URL', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}base-url",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('App UUID', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}app-uuid",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client ID', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}client-id",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client Secret', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}client-secret",
                        'required'          => true
                    ]
                ],
            ],
            [
                'title'  => esc_html__('Organization', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('RSIN', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}rsin",
                        'required'          => true,
                        'description'       => 'Registration number for non-natural persons, also known as the counterpart of the citizen service number (BSN).'
                    ]
                ],
            ]
        ];
    }
}
