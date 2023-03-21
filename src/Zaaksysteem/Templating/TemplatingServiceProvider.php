<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Templating;

use OWC\Zaaksysteem\Foundation\ServiceProvider;

use function OWC\Zaaksysteem\Foundation\Helpers\config;

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
            $postTemplates['template-openzaak.php'] = esc_html__('OpenZaak', config('core.text_domain'));

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
