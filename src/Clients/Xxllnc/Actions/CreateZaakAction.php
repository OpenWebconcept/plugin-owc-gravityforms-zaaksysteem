<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\Xxllnc\Actions;

use Exception;
use OWC\Zaaksysteem\Contracts\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Entities\Zaak;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CLIENT_NAME = 'xxllnc';
    public const CALLABLE_NAME = 'xxllnc.client';
    public const CLIENT_CATALOGI_URL = 'xxllnc.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'xxllnc.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'xxllnc';

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): Zaak
    {
        $rsin = $this->getRSIN();

        if (empty($rsin)) {
            throw new Exception('Het RSIN is niet ingesteld in de Gravity Forms instellingen');
        }

        $zaaktype = $this->getZaakType($form);

        if (empty($zaaktype)) {
            throw new Exception('Het zaaktype is niet ingesteld in de Gravity Forms instellingen');
        }

        $client = $this->getApiClient();

        $args = $this->mappedArgs($rsin, $zaaktype, $form, $entry);
        $zaak = $client->zaken()->create(new Zaak($args, $client->getClientName(), $client->getClientNamePretty()));

        // Complement Zaak.
        $this->addRolToZaak($zaak, $zaaktype['url']);
        $this->addZaakEigenschappen($zaak, $form['fields'], $entry);

        return $zaak;
    }
}
