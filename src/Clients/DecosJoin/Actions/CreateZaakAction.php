<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin\Actions;

use Exception;
use OWC\Zaaksysteem\Contracts\AbstractCreateZaakAction;
use OWC\Zaaksysteem\Entities\Zaak;

class CreateZaakAction extends AbstractCreateZaakAction
{
    public const CALLABLE_NAME = 'dj.client';
    public const CLIENT_CATALOGI_URL = 'dj.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'dj.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'decos-join';

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): ?Zaak
    {
        $rsin = $this->getRSIN();

        if (empty($rsin)) {
            throw new Exception('Het RSIN is niet ingesteld in de Gravity Forms instellingen.');
        }

        $zaaktype = $this->getZaakType($form);

        if (empty($zaaktype)) {
            throw new Exception('Het zaaktype is niet ingesteld in de Gravity Forms instellingen.');
        }

        $client = $this->getApiClient();

        $args = $this->mappedArgs($rsin, $zaaktype, $form, $entry);
        $zaak = $client->zaken()->create(new Zaak($args, $client->getClientName()));

        // REFERENCE POINT: Mike, adding 'Rol' and 'Zaak Eigenschappen' does not work yet.
        //$this->addRolToZaak($zaak, $zaaktype['url']); // -> returns 'Bad request "zaaktype mandatory parameter not provided."
        // $this->addZaakEigenschappen($zaak, $form['fields'], $entry); -> returns 'Bad request "zaaktype mandatory parameter not provided."

        return $zaak;
    }
}
