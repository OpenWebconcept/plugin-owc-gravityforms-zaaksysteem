<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use Exception;
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
        // Bail early when in editor.
        if (is_admin()) {
            return;
        }

        $this->client = ContainerResolver::make()->getApiClient($attributes['zaakClient'] ?? 'openzaak');

        if (! $this->client->supports('zaken')) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        if (! $attributes['combinedClients']) {
            $zaken = $this->getZaken($attributes);
        } else {
            $zaken = $this->getCombinedZaken($attributes);
        }

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

    protected function getCombinedZaken(array $attributes): Collection
    {
        $zaken = [];
        $suppliers = ContainerResolver::make()->get('config')->get('suppliers');

        foreach (array_keys($suppliers) as $supplier) {
            $client = ContainerResolver::make()->getApiClient($supplier);
            $filter = new ZakenFilter();
            $filter = $this->handleFilterBSN($filter, $attributes);
            $filter = $this->handleFilterZaaktype($filter, $attributes, $client);

            try {
                $zaken[] = $client->zaken()->filter($filter)->all();
            } catch(Exception $e) {
                continue;
            }
        }

        return Collection::collect($zaken)->flattenAndAssign(function ($carry, $item) {
            if (is_array($item)) {
                return array_merge($carry, $item);
            }

            $carry[] = $item;

            return $carry;
        }, []);
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

    protected function handleFilterZaaktype(ZakenFilter $filter, array $attributes, ?Client $client = null): ZakenFilter
    {
        if (! is_string($attributes['zaaktypeFilter'])) {
            return $filter;
        }

        $identifications = json_decode($attributes['zaaktypeFilter'], true);

        if (! is_array($identifications) || empty($identifications)) {
            return $filter;
        }

        foreach ($this->zaaktypeIdentificationsToURL($identifications, $client) as $zaaktype) {
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
    protected function zaaktypeIdentificationsToURL(array $identifications, ?Client $client = null): array
    {
        $page = 1;
        $zaaktypen = [];

        $client = $client ? $client : $this->client; // Use $client when 'zaken' from all the suppliers are retrieved combined.

        while ($page) {
            $result = $client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
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
