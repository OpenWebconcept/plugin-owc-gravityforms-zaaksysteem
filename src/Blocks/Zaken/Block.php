<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Traits\ResolveBSN;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class Block
{
    use ResolveBSN;

    public function render($attributes, $rendered, $editor)
    {
        $client = $this->getApiClient($attributes);

        if (! $client->supports('zaken')) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        $zaken = $this->getZaken($client, $attributes);

        if ($zaken->isEmpty()) {
            return 'Er zijn op dit moment geen zaken beschikbaar.';
        }

        if ($attributes['view'] === 'tabs') {
            return view('blocks/mijn-zaken/overview-zaken-tabs.php', ['zaken' => $zaken]);
        }

        return view('blocks/mijn-zaken/overview-zaken.php', ['zaken' => $zaken]);
    }

    protected function getApiClient(array $attributes): Client
    {
        $client = $attributes['zaakClient'] ?? 'openzaak';

        switch ($client) {
            case 'decosjoin':
                $client = resolve('dj.client');

                return $client;
            case 'rx-mission':
                return resolve('rx.client');
            case 'openzaak': // fallthrough.
            default:
                return resolve('oz.client');
        }
    }

    protected function getZaken(Client $client, array $attributes): Collection
    {
        $filter = new ZakenFilter();

        $filter = $this->handleFilterBSN($filter, $attributes);
        $filter = $this->handleFilterZaaktype($filter, $attributes);

        return $client->zaken()->filter($filter);
    }

    protected function handleFilterBSN(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! $attributes['byBSN']) {
            return $filter;
        }

        $currentBsn = $this->resolveCurrentBsn();
        $filter->byBsn($currentBsn);

        return $filter;
    }

    protected function handleFilterZaaktype(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! is_string($attributes['zaaktypeFilter'])) {
            return $filter;
        }

        $zaaktypes = json_decode($attributes['zaaktypeFilter'], true);

        if (! is_array($zaaktypes) || empty($zaaktypes)) {
            return $filter;
        }

        foreach($zaaktypes as $zaaktype) {
            $filter->add('zaaktype', $zaaktype);
        }

        return $filter;
    }
}
