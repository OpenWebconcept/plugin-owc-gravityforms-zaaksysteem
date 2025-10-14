<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\WPCron\Events;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\GravityForms\GravityFormsSettings;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Adapters\ClientAdapter;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Services\TypeRetrievalService;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Support\TypeCache;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class GravityFormsFormSettings
{
    protected ContainerResolver $container;

    public function __construct()
    {
        $this->container = ContainerResolver::make();
    }

    /**
     * Initializes the process of retrieving types from the ZGW client for all enabled suppliers.
     *
     * This method obtains a list of enabled suppliers and, for each supplier,
     * fetches the corresponding zaaktypen and informatieobjecttypen from the ZGW client which are cached for use in Gravity Forms form settings.
     */
    public function init(): void
    {
        $enabled = $this->getEnabledSuppliers();

        foreach ($enabled as $supplier) {
            $this->fetchTypes($supplier);
        }
    }

    /**
     * @return array<array{client:string, class:class-string<ClientAdapter>}>
     */
    protected function getEnabledSuppliers(): array
    {
        $suppliers = $this->container->get('config')->get('suppliers', []);

        if ([] === $suppliers) {
            return [];
        }

        $gfSettings = GravityFormsSettings::make();
        $enabled = [];

        foreach ($suppliers as $name => $label) {
            $enabledSupplier = $gfSettings->get(sprintf('-suppliers-%s-enabled', $name));

            if ('1' !== $enabledSupplier) {
                continue;
            }

            $supplierClientClass = sprintf(
                'OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\%sClient',
                $label
            );

            if (! class_exists($supplierClientClass)) {
                continue;
            }

            $enabled[] = ['client' => $label, 'class' => $supplierClientClass];
        }

        return $enabled;
    }

    /**
     * Triggers the form settings fetching logic for a given supplier.
     *
     * @param array{client:string, class:class-string<ClientAdapter>} $supplier
     */
    protected function fetchTypes(array $supplier): void
    {
        /** @var Client $client */
        $client = $this->container->getApiClient($supplier['client']);

        try {
            /** @var ClientAdapter $clientAdapter */
            $clientAdapter = new $supplier['class']($client->getClientNamePretty(), new TypeRetrievalService($client), new TypeCache());
        } catch (Exception $e) {
            $this->container->get('message.logger')->error('Error initializing client adapter', ['exception' => $e]);

            return;
        }

        $clientAdapter->setIsCron(true)->setTimeout(30);
        $clientAdapter->zaaktypen();
        $clientAdapter->informatieobjecttypen();
    }
}
