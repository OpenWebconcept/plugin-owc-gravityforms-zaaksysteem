<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\OpenZaak\Actions;

use Exception;
use OWC\Zaaksysteem\Contracts\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Endpoints\Filter\RoltypenFilter;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Support\PagedCollection;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CLIENT_NAME = 'openzaak';
    public const CALLABLE_NAME = 'oz.client';
    public const CLIENT_CATALOGI_URL = 'oz.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'oz.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'openzaak';

    /**
     * Get all available "roltypen".
     */
    public function getRolTypen(string $zaaktype): PagedCollection
    {
        $client = $this->getApiClient();

        $filter = new RoltypenFilter();
        $filter->get('zaaktype', $zaaktype);

        return $client->roltypen()->filter($filter);
    }

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): Zaak
    {
        $rsin = $this->getRSIN();

        if (empty($rsin)) {
            throw new Exception('Het RSIN is niet ingesteld in de Gravity Forms instellingen');
        }

        $zaaktypeURL = $this->getZaakTypeURL($form);

        if (empty($zaaktypeURL)) {
            throw new Exception('Het zaaktype is niet ingesteld in de Gravity Forms instellingen');
        }

        $client = $this->getApiClient();

        $args = $this->mappedArgs($rsin, $zaaktypeURL, $form, $entry);
        $zaak = $client->zaken()->create(new Zaak($args, $client->getClientName(), $client->getClientNamePretty()));

        // Complement Zaak.
        $branchNumberKVK = $this->getPossibleBranchNumberKVK($form, $entry);
        $this->addRolToZaak($zaak, $zaaktypeURL, $branchNumberKVK);
        $this->addZaakEigenschappen($zaak, $form['fields'], $entry);

        return $zaak;
    }
}
