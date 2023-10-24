<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class Block
{
    protected Client $client;

    public function render($attributes, $rendered, $editor)
    {
        $this->client = ContainerResolver::make()->getApiClient($attributes['zaakClient'] ?? 'openzaak');

        if (! $this->client->supports('zaken')) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        $zaken = $this->getZaken($attributes);

        if ($zaken->isEmpty()) {
            return 'Er zijn op dit moment geen zaken beschikbaar.';
        }

        if ($attributes['view'] === 'tabs') {
            return view('blocks/mijn-zaken/overview-zaken-tabs.php', ['zaken' => $zaken]);
        }

        return view('blocks/mijn-zaken/overview-zaken.php', ['zaken' => $zaken]);
    }

    protected function getZaken(array $attributes): Collection
    {
        $filter = new ZakenFilter();

        $filter = $this->handleFilterBSN($filter, $attributes);
        $filter = $this->handleFilterZaaktype($filter, $attributes);

        return $this->client->zaken()->filter($filter);
    }

    protected function handleFilterBSN(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! $attributes['byBSN']) {
            return $filter;
        }

        $currentBsn = resolve('digid.current_user_bsn');
        $filter->byBsn($currentBsn);

        return $filter;
    }

    protected function handleFilterZaaktype(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! is_string($attributes['zaaktypeFilter'])) {
            return $filter;
        }

        $identifications = json_decode($attributes['zaaktypeFilter'], true);

        if (! is_array($identifications) || empty($identifications)) {
            return $filter;
        }

        foreach ($this->zaaktypeIdentificationsToURL($identifications) as $zaaktype) {
            $filter->add('zaaktype', $zaaktype);
            // $filter->add('identificatie', $zaaktype); // Is not supported yet.
        }

        return $filter;
    }

    /**
     * For testing purposes, use 'JB007' as identification.
     * This method should be removed when filtering on 'Zaaktype' identification is supported.
     * Don't forget to unuse this method in the foreach above as well.
     */
    protected function zaaktypeIdentificationsToURL(array $identifications): array
    {
        $page = 1;
        $zaaktypen = [];

        while ($page) {
            $result = $this->client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        return (array) Collection::collect($zaaktypen)->map(function (Zaaktype $zaaktype) use ($identifications) {
            if (! in_array($zaaktype->identificatie, $identifications)) {
                return '';
            }

            return $zaaktype->url;
        })->filter(function ($url) {
            return ! empty($url);
        })->all();
    }
}
