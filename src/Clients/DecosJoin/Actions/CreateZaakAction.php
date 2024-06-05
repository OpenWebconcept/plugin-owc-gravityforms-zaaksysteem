<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin\Actions;

use Exception;
use OWC\Zaaksysteem\Contracts\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Entities\Zaak;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CLIENT_NAME = 'decos-join';
    public const CALLABLE_NAME = 'dj.client';
    public const CLIENT_CATALOGI_URL = 'dj.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'dj.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'decos-join';

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): Zaak
    {
        $rsin = $this->getRSIN();

        if (empty($rsin)) {
            throw new Exception('Het RSIN is niet ingesteld in de Gravity Forms instellingen.');
        }

        $zaaktypeURL = $this->getZaakTypeURL($form);

        if (empty($zaaktypeURL)) {
            throw new Exception('Het zaaktype is niet ingesteld in de Gravity Forms instellingen.');
        }

        $client = $this->getApiClient();
        $args = $this->mappedArgs($rsin, $zaaktypeURL, $form, $entry);
        $zaak = $client->zaken()->create(new Zaak($args, $client->getClientName(), $client->getClientNamePretty()));

        // Complement Zaak.
        $this->addRolToZaak($zaak, $zaaktypeURL);
        $this->addZaakEigenschappen($zaak, $form['fields'], $entry);

        return $zaak;
    }
}
