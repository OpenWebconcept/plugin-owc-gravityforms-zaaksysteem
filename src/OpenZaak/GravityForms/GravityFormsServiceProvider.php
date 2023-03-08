<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use OWC\OpenZaak\Foundation\ServiceProvider;

class GravityFormsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerSettingsAddon();
        $this->loadHooks();
    }

    protected function registerSettingsAddon(): void
    {
        if (! method_exists('\GFForms', 'include_addon_framework')) {
            return;
        }

        \GFForms::include_addon_framework();
        \GFAddOn::register(GravityFormsAddOnSettings::class);
        GravityFormsAddOnSettings::get_instance();
    }

    protected function loadHooks(): void
    {
        $this->plugin->loader->addFilter('gform_after_submission', new GravityForms(), 'afterSubmission', 10, 2);
    }
}
