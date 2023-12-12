<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing\Controllers;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaak;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
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
     * - the page to be requested with an download identification, zaak identification and a supplier in the URI
     */
    protected function addCustomRewriteRules(): void
    {
        add_action('generate_rewrite_rules', function (WP_Rewrite $rewrite) {
            $rules = [
                'zaak-download/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z-]+)/?$' => 'index.php?pagename=zaak-download&zaak_download_identification=$matches[1]&zaak_identification=$matches[2]&zaak_supplier=$matches[3]',
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
            $queryVars[] = 'zaak_supplier';
            $queryVars[] = 'zaak_identification';
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
        $downloadIdentification = get_query_var('zaak_download_identification');
        $zaakIdentification = get_query_var('zaak_identification');
        $supplier = get_query_var('zaak_supplier');

        if (empty($downloadIdentification) || empty($zaakIdentification) || empty($supplier)) {
            return;
        }

        if (! $this->checkSupplier($supplier)) {
            return;
        }

        $client = $this->container->getApiClient($supplier);

        if (! $this->validateZaak($client, $zaakIdentification)) {
            return;
        }

        try {
            $binary = $client->enkelvoudiginformatieobjecten()->download($downloadIdentification);
        } catch(Exception $e) {
            return;
        }

        $file = sprintf('%s.pdf', $downloadIdentification);
        file_put_contents($file, $binary);

        if (! file_exists($file)) {
            return;
        }

        $this->initiateFileDownload($file);
    }

    protected function validateZaak(Client $client, string $zaakIdentification): ?Zaak
    {
        try {
            $filter = new ZakenFilter();
            $filter->add('identificatie', $zaakIdentification);
            $filter->byBsn(resolve('digid.current_user_bsn'));
            $zaak = $client->zaken()->filter($filter)->first() ?: null;
        } catch(Exception $e) {
            $zaak = null;
        }

        return $zaak;
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
