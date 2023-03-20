<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Templating;

use OWC\OpenZaak\Foundation\ServiceProvider;

class TemplatingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadHooks();
    }

    protected function loadHooks(): void
    {
        /**
         * Add template from this plugin to page attributes template section.
         */
        add_filter('theme_page_templates', function ($postTemplates) {
            $postTemplates['template-openzaak.php'] = __('OpenZaak');

            return $postTemplates;
        }, 10, 4);

        /**
         * Load template from this plugin when selected in page attributes template section.
         */
        add_filter('page_template', function ($pageTemplate) {
            if (get_page_template_slug() === 'template-openzaak.php') {
                $pageTemplate = sprintf('%s/%s', OZ_ROOT_PATH, 'resources/views/template-openzaak.php');
            }

            return $pageTemplate;
        }, 10, 1);
    }
}
