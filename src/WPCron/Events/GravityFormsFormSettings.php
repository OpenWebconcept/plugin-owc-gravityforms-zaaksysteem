<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\WPCron\Events;

use Exception;
use OWC\Zaaksysteem\GravityForms\GravityFormsSettings;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Adapters\ClientAdapter;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class GravityFormsFormSettings
{
    private const SUPPLIER_KEY_REGEX = '#^owc-gravityforms-zaaksysteem-suppliers-([a-z0-9_-]+)-enabled$#i';

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

        if (! is_array($suppliers) || [] === $suppliers) {
            return [];
        }

        $enabled = [];
        $isEnabledSetting = '1';

        foreach (GravityFormsSettings::make()->all() as $key => $value) {
            if ($isEnabledSetting !== $value) {
                continue;
            }

            // Match keys like 'owc-gravityforms-zaaksysteem-suppliers-<supplier>-enabled' and extract the supplier identifier.
            if (! preg_match(self::SUPPLIER_KEY_REGEX, $key, $match)) {
                continue;
            }

            $supplierIdentifier = $match[1] ?? '';

            if (! array_key_exists($supplierIdentifier, $suppliers)) {
                continue;
            }

            $supplierClientClass = sprintf('OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\%sClient', $suppliers[$supplierIdentifier]);

            if (! class_exists($supplierClientClass)) {
                continue;
            }

            $enabled[] = ['client' => $supplierIdentifier, 'class' => $supplierClientClass];
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
        try {
            /** @var ClientAdapter $client */
            $client = new $supplier['class']($this->container->getApiClient($supplier['client']));
        } catch (Exception $e) {
            return;
        }

        $client->setIsCron(true)->setTimeout(30);
        $client->zaaktypen();
        $client->informatieobjecttypen();
    }
}
