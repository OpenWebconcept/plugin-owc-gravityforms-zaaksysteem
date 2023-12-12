<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use GFAddOn;

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
            $this->settingsDescription(),
            $this->settingsOpenZaak(),
            $this->settingsDecosJoin(),
            $this->settingsRxMission(),
            $this->settingsXxllnc(),
        ];
    }

    protected function settingsGeneral(): array
    {
        return [
            'title' => esc_html__('Algemene instellingen', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'label' => esc_html__('RSIN-nummer', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rsin",
                    'description' => 'Registratienummer voor organisaties. Wordt door (overheids)organisaties gebruikt om informatie met elkaar uit te wisselen.',
                ],
                [
                    'label' => esc_html__('Zaak afbeelding', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'name' => "{$this->prefix}-zaak-image",
                    'description' => esc_html__('Stel de header-afbeelding in voor een zaak. Laat leeg voor een standaard grijze achtergrond.'),
                ],
            ],
        ];
    }

    protected function settingsDescription(): array
    {
        return [
            'title' => esc_html__('Toelichting', 'owc-gravityforms-zaaksysteem'),
            'description' => esc_html__('Voer de gegevens in van de leveranciers die je wilt gebruiken. Voeg geen endpoint-type toe, dus "https://supplier.com/api/v1/" in plaats van "https://supplier.com/api/v1/eigenschappen".', 'owc-gravityforms-zaaksysteem'),
            'fields' => [[
                'type' => '',
            ]],
        ];
    }

    protected function settingsOpenZaak(): array
    {
        return [
            'title' => esc_html__('OpenZaak', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-openzaak-client-secret",
                ],
            ],
        ];
    }

    protected function settingsDecosJoin(): array
    {
        return [
            'title' => esc_html__('Decos Join', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret (ZTC)', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-decos-join-client-secret",
                ],
                [
                    'label' => esc_html__('Client Secret (ZRC)', 'owc-gravityforms-zaaksysteem'),
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
            'title' => esc_html__('Rx.Mission', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-rx-mission-client-secret",
                ],
            ],
        ];
    }

    protected function settingsXxllnc(): array
    {
        return [
            'title' => esc_html__('Xxllnc', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'label' => esc_html__('Catalogi URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-xxllnc-catalogi-url",
                ],
                [
                    'label' => esc_html__('Documenten URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-xxllnc-documenten-url",
                ],
                [
                    'label' => esc_html__('Zaken URL', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-xxllnc-zaken-url",
                ],
                [
                    'label' => esc_html__('Client ID', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-xxllnc-client-id",
                ],
                [
                    'label' => esc_html__('Client Secret', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'text',
                    'class' => 'medium',
                    'name' => "{$this->prefix}-xxllnc-client-secret",
                ],
            ],
        ];
    }
}
