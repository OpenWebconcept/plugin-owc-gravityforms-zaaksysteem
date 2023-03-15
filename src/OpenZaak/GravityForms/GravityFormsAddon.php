<?php

namespace OWC\OpenZaak\GravityForms;

use GFAddOn;

class GravityFormsAddon extends GFAddOn
{
    /**
     * Version number.
     *
     * @var string
     */
    protected $_version = OZ_VERSION;

    /**
     * Minimal required GF version.
     *
     * @var string
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
     *
     * @var string
     */
    protected $_path = OZ_ROOT_PATH . '/plugin.php';

    /**
     * The physical path to the main plugin file.
     *
     * @var string
     */
    protected $_full_path = __FILE__;

    /**
     * The complete title of the Add-On.
     *
     * @var string
     */
    protected $_title = 'OWC OpenZaak';

    /**
     * The short title of the Add-On to be used in limited spaces.
     *
     * @var string
     */
    protected $_short_title = 'OWC OpenZaak';

    /**
     * Instance object
     *
     * @var self
     */
    private static $_instance = null;

    /**
     * Singleton loader.
     *
     * @return self
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
        $prefix = 'owc-openzaak-';
        return [
            [
                'title'  => esc_html__('Service Provider (SP)', 'owc-openzaak'),
                'fields' => [
                    [
                        'label'             => esc_html__('URL', 'owc-openzaak'),
                        'type'              => 'text',
                        'input_type'        => 'url',
                        'class'             => 'medium',
                        'name'              => "{$prefix}sp-url",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client ID', 'owc-openzaak'),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}sp-client-id",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Client secret', 'owc-openzaak'),
                        'type'              => 'text',
                        'input_type'        => 'password',
                        'class'             => 'medium',
                        'name'              => "{$prefix}sp-client-secret",
                        'required'          => true
                    ],
                ],
            ],
        ];
    }
}
