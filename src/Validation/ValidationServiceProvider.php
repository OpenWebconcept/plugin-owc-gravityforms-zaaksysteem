<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Validation;

use OWC\Zaaksysteem\Foundation\ServiceProvider;

use function Yard\DigiD\Foundation\Helpers\resolve;

class ValidationServiceProvider extends ServiceProvider
{
    protected object $session;

    public function __construct()
    {
        $this->session = resolve('session')->getSegment('digid');
    }

    public function boot(): void
    {
        $this->loadHooks();
    }

    protected function loadHooks(): void
    {
        /**
         * Validate private pages on template.
         * Check if BSN is present in current user session.
         */
        add_action('template_include', function ($template) {
            $templateName = str_replace(['.blade.php', '.php'], '', basename($template));

            /**
             * Filters the array of templates to validate.
             *
             * @since 1.1.1
             *
             * @param array $templatesToValidate Template names to validate
             */
            $templatesToValidate = apply_filters('owc_gravityforms_zaaksysteem_templates_to_validate', ['template-openzaak']);

            if (! in_array($templateName, $templatesToValidate)) {
                return $template;
            }

            if (empty($this->session->get('bsn', ''))) {
                return $this->returnForbidden();
            }

            return $template;
        }, 10, 1);
    }

    /**
     * Return a 403 forbidden page if the user has no access rights.
     *
     * @since 1.0.0
     */
    private function returnForbidden(): string
    {
        global $wp_query;

        $wp_query->set_403();

        status_header(403);

        add_filter('pre_get_document_title', function ($title) {
            return 'Geen toegang';
        }, 10, 1);

        return sprintf('%s/%s', OWC_GZ_ROOT_PATH, 'resources/views/403.php');
    }
}
