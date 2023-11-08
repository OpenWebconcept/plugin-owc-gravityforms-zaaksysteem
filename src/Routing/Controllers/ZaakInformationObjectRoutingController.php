<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing\Controllers;

use Exception;
use WP_Rewrite;

class ZaakInformationObjectRoutingController extends AbstractRoutingController
{
    public function register(): void
    {
        $this->addCustomRewriteRules();
        $this->allowCustomQueryVars();
        $this->includeObjectInTemplate();
    }

    /**
     * Implement custom routing for routing for downloading 'zaak' information objects.
     *
     * The implementation for downloading 'zaak' information objects requires:
     * - a page with 'zaak-download' as the slug
     * - the page to be requested with an identification and supplier in the URI
     */
    protected function addCustomRewriteRules(): void
    {
        add_action('generate_rewrite_rules', function (WP_Rewrite $rewrite) {
            $rules = [
                'zaak-download/([a-zA-Z0-9-]+)/([a-zA-Z]+)/?$' => 'index.php?pagename=zaak-download&zaak_download_identification=$matches[1]&zaak_supplier=$matches[2]',
            ];

            $rewrite->rules = $rules + $rewrite->rules;
        });
    }

    /**
     * Add requested 'zaak' and information object vars to the query variables.
     */
    protected function allowCustomQueryVars(): void
    {
        add_action('query_vars', function (array $queryVars) {
            $queryVars[] = 'zaak_identification';
            $queryVars[] = 'zaak_supplier';
            $queryVars[] = 'zaak_download_identification';

            return array_unique($queryVars);
        });
    }

    protected function includeObjectInTemplate(): void
    {
        add_action('template_include', function ($template) {
            $this->handleZaakDownload();

            return $template;
        }, 999, 1); // High priority so the validateTemplate method inside the ValidationServiceProvider runs first.
    }

    protected function handleZaakDownload(): void
    {
        $identification = get_query_var('zaak_download_identification');
        $supplier = get_query_var('zaak_supplier');

        if (empty($identification) || empty($supplier)) {
            return;
        }

        if (! $this->checkSupplier($supplier)) {
            return;
        }

        $client = $this->container->getApiClient($supplier);

        try {
            $binary = $client->enkelvoudiginformatieobjecten()->download($identification);
        } catch(Exception $e) {
            return;
        }

        $file = sprintf('%s.pdf', $identification);
        file_put_contents($file, $binary);

        if (! file_exists($file)) {
            return;
        }

        $this->initiateFileDownload($file);
    }

    /**
     * Send headers, read file and finish with removing the file from temporary location.
     */
    protected function initiateFileDownload(string $file): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }
}
