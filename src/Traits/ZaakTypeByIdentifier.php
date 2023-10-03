<?php

namespace OWC\Zaaksysteem\Traits;

use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Support\Collection;

trait ZaakTypeByIdentifier
{
    /**
     * Get the zaaktype belonging to the chosen zaaktype identifier.
     */
    public function zaakTypeByIdentifier(Client $client, string $zaaktypeIdentifier): ?Zaaktype
    {
        $page = 1;
        $zaaktypen = [];

        while ($page) {
            $result = $client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        return Collection::collect($zaaktypen)->filter(
            function (Zaaktype $zaaktype) use ($zaaktypeIdentifier) {
                if ($zaaktype->identificatie === $zaaktypeIdentifier) {
                    return $zaaktype;
                }
            }
        )->first() ?: null;
    }
}
