<?php declare(strict_types=1);

namespace OWC\OpenZaak\Validation;

use OWC\OpenZaak\Foundation\ServiceProvider;
use function Yard\DigiD\Foundation\Helpers\resolve;

class ValidationServiceProvider extends ServiceProvider
{
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
        \add_action('template_include', function ($template) {
            $templateName = str_replace(['.blade.php', '.php'], '', basename($template));
            $templateToValidate = 'template-pip';
            
            if ($templateName !== $templateToValidate) {
                return $template;
            }

            if (empty($this->session->get('bsn', ''))) {
                return $this->returnForbidden();
            }

            return $template;
        }, 10, 1);
    }

    private function returnForbidden(): string
    {
        global $wp_query;
        $wp_query->set_403();
        status_header(403);

        if (method_exists('\App\Traits\SEO', 'setDocumentTitle')) {
            $this->setDocumentTitle('Geen toegang');
        }

        return sprintf('%s/%s', OZ_ROOT_PATH, 'resources/views/403.php');
    }
}
