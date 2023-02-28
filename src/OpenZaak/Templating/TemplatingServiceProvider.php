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
         * Add template from this plugin to page attribute template section.
         */
        \add_filter('theme_page_templates', function ($post_templates, $wp_theme, $post, $post_type) {
            $post_templates['template-pip.php'] = __('PIP');

            return $post_templates;
        }, 10, 4);

        /**
         * Load template from this plugin when selected in page attribute template section.
         */
        \add_filter('page_template', function ($page_template) {
            if (\get_page_template_slug() === 'template-pip.php') {
                $page_template = sprintf('%s/%s', OZ_ROOT_PATH, 'resources/views/template-pip.php');
            }

            return $page_template;
        }, 10, 1);
    }
}
