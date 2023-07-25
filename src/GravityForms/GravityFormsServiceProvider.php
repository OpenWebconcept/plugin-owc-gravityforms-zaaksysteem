<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use GFAddOn;
use GFForms;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\GravityForms\GravityFormsFieldSettings;

class GravityFormsServiceProvider extends ServiceProvider
{
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
    }

    public function boot(): void
    {
        $this->loadHooks();
        $this->registerSettingsAddon();
    }

    protected function loadHooks(): void
    {
        if (! class_exists('GFForms')) {
            return;
        }

        $gravityFormsFieldSettings = new GravityFormsFieldSettings($this->plugin);

        $this->plugin->loader->addFilter('gform_after_submission', new GravityForms($this->plugin), 'GFFormSubmission', 10, 2);
        $this->plugin->loader->addAction('gform_field_standard_settings', $gravityFormsFieldSettings, 'addSelect', 10, 2);
        $this->plugin->loader->addAction('gform_editor_js', $gravityFormsFieldSettings, 'addSelectScript', 10, 0);
        $this->plugin->loader->addFilter('gform_form_settings_fields', new GravityFormsFormSettings($this->plugin), 'addFormSettings', 10, 2);
    }

    private function registerSettingsAddon(): void
    {
        if (! method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        GFForms::include_addon_framework();
        GFAddOn::register(GravityFormsAddon::class);
        GravityFormsAddon::get_instance();
    }
}
