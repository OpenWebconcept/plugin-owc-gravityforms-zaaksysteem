<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing;

use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class RoutingServiceProvider extends ServiceProvider
{
    protected ContainerResolver $container;

    public function __construct()
    {
        $this->container = ContainerResolver::make();
    }

    public function boot(): void
    {
        $this->addCustomRewriteRule();
        $this->allowCustomQueryVars();
        $this->zaakToTemplate();
    }

    /**
     * Implement custom routing for single 'zaak' pages.
     *
     * This implementation requires:
     * - a page with 'zaak' as the slug
     * - the page to be connected with template 'template-single-zaak'
     */
    protected function addCustomRewriteRule(): void
    {
        add_action('init', function () {
            flush_rewrite_rules();
            add_rewrite_rule(
                'zaak/([a-zA-Z0-9-]+)/([a-zA-Z]+)/?$',
                'index.php?pagename=zaak&zaak_identification=$matches[1]&zaak_supplier=$matches[2]',
                'top'
            );
        });
    }

    protected function allowCustomQueryVars(): void
    {
        add_action('query_vars', function (array $queryVars) {
            $queryVars[] = 'zaak_identification';
            $queryVars[] = 'zaak_supplier';

            return $queryVars;
        });
    }

    /**
     * Add requested 'zaak' to the query variables.
     * This enables using the 'zaak' inside the template.
     */
    protected function zaakToTemplate(): void
    {
        add_action('template_include', function ($template) {
            $templateName = str_replace(['.blade.php', '.php'], '', basename($template));

            if ($templateName !== 'template-single-zaak') {
                return $template;
            }

            set_query_var('zaak', $this->getZaak());

            return $template;
        }, 999, 1); // High priority so the validateTemplate method inside the ValidationServiceProvider runs first.
    }

    protected function getZaak(): ?Zaak
    {
        $identification = get_query_var('zaak_identification');
        $supplier = get_query_var('zaak_supplier');

        if (empty($identification) || empty($supplier)) {
            return null;
        }

        if (! $this->checkSupplier($supplier)) {
            return null;
        }

        $client = $this->container->getApiClient($supplier);

        $filter = new ZakenFilter();
        $filter->add('identificatie', $identification);
        $filter->byBsn(resolve('digid.current_user_bsn'));

        return $client->zaken()->filter($filter)->first() ?: null;
    }

    /**
     * The supplier is retrieved from the requested URL therefore making it vulnerable for unwanted changes.
     */
    protected function checkSupplier(string $supplier): bool
    {
        $suppliers = $this->container->get('config')->get('suppliers');

        return in_array($supplier, array_keys($suppliers));
    }
}
