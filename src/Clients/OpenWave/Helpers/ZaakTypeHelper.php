<?php

namespace OWC\Zaaksysteem\Clients\OpenWave\Helpers;

use OWC\Zaaksysteem\Entities\ZaakType;

class ZaakTypeHelper
{
    public static function handleOpenWaveZaaktypeByIdentifier($client, string $zaaktypeIdentifier): ?ZaakType
    {
        if (empty($zaaktypeIdentifier)) {
            return null;
        }

        $page = 1;
        $types = [];

        while ($page) {
            try {
                // $result = $client->zaaktypen()->all((new \OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter())->page($page));
                $result = $client->zaaktypen()->all();
            } catch (\Exception $e) {
                break;
            }

            $types = array_merge($types, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        $zaaktype = null;

        foreach ($types as $type) {
            if ($type->identificatie === $zaaktypeIdentifier) {
                $zaaktype = $type;

                break;
            }
        }

        return $zaaktype;
    }
}
