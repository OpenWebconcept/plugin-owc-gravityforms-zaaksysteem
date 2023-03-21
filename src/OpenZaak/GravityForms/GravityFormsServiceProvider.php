<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use GFAddOn;
use GFForms;

use OWC\OpenZaak\Foundation\ServiceProvider;

class GravityFormsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadHooks();
        $this->registerSettingsAddon();
    }

    protected function loadHooks(): void
    {
        if (!class_exists('GFForms')) {
            return;
        }

        $this->plugin->loader->addFilter('gform_after_submission', new GravityForms(), 'afterSubmission', 10, 2);
        $this->plugin->loader->addAction('gform_field_standard_settings', new GravityFormsFieldSettings(), 'addSelect', 10, 2);
        $this->plugin->loader->addFilter('gform_form_settings_fields', new GravityFormsFormSettings(), 'addFormSettings', 10, 2);
    }

    private function registerSettingsAddon(): void
    {
        if (!method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        GFForms::include_addon_framework();
        GFAddOn::register(GravityFormsAddon::class);
        GravityFormsAddon::get_instance();
    }
}
