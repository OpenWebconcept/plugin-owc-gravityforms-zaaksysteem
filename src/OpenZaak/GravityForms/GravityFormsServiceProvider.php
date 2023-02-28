<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use OWC\OpenZaak\Foundation\ServiceProvider;

class GravityFormsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadHooks();
    }

    protected function loadHooks(): void
    {
        $this->plugin->loader->addFilter('gform_after_submission', new GravityForms(), 'afterSubmission', 10, 2);
    }
}
