<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin\Actions;

use OWC\Zaaksysteem\Contracts\AbstractDeleteZaakAction;

class DeleteZaakAction extends AbstractDeleteZaakAction
{
    public const CLIENT_NAME = 'decos-join';

    public function deleteZaak(string $zaakURL, array $entry, array $form): void
    {
        $client = $this->getApiClient();
        $client->zaken()->delete($zaakURL);
    }
}
