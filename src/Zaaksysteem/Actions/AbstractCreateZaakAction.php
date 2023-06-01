<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Actions;

use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Traits\ResolveBSN;

abstract class AbstractCreateZaakAction
{
    use ResolveBSN;

    public const CALLABLE_NAME = '';
    public const CLIENT_CATALOGI_URL = '';
    public const CLIENT_ZAKEN_URL = '';
    public const FORM_SETTING_SUPPLIER_KEY = '';
    
    protected Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    protected function getApiClient(): Client
    {
        return $this->plugin->getContainer()->get(static::CALLABLE_NAME);
    }

    protected function getCatalogiURL(): string
    {
        return $this->plugin->getContainer()->get(static::CLIENT_CATALOGI_URL);
    }

    protected function getZakenURL(): string
    {
        return $this->plugin->getContainer()->get(static::CLIENT_ZAKEN_URL);
    }
    
    abstract public function getRolTypen(string $zaaktype): PagedCollection;
    
    /**
     * Use the selected `zaaktype identifier` to retrieve the `zaaktype`.
     *
     * @todo we cannot use the zaaktype URI to retrieve a zaaktype because it is bound to change when the zaaktype is updated. There doesn't seem to be a way to retrieve the zaaktype by identifier, so we have to get all the zaaktypen first and then filter them by identifier. We should change this when the API supports this.
     *
     * @see https://github.com/OpenWebconcept/plugin-owc-gravityforms-zaaksysteem/issues/13#issue-1697256063
     */
    public function getZaakType($form): ?Zaaktype
    {
        $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, static::FORM_SETTING_SUPPLIER_KEY)];
        $client = $this->getApiClient();
        $client->setEndpointURL($this->getCatalogiURL());

        return $client->zaaktypen()->all()->filter(
            function (Zaaktype $zaaktype) use ($zaaktypeIdentifier) {
                if ($zaaktype->identificatie === $zaaktypeIdentifier) {
                    return $zaaktype;
                }
            }
        )->first();
    }

    abstract public function addZaak($entry, $form): ?Zaak;
    abstract public function addZaakEigenschappen(Zaak $zaak, $fields, $entry): void;
    abstract public function addRolToZaak(Zaak $zaak, string $zaaktype): ?Rol;
}
