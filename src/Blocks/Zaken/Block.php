<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaaktype;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

use function OWC\Zaaksysteem\Foundation\Helpers\view;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;

class Block
{
    protected Client $client;
    protected string $currentUserBsn;

    public function __construct()
    {
        $this->currentUserBsn = resolve('digid.current_user_bsn');
    }

    public function render($attributes, $rendered, $editor)
    {
        // Bail early when in editor.
        if (is_admin()) {
            return;
        }

        if (empty($this->currentUserBsn)) {
            return 'Er is geen geldig BSN gevonden waardoor er geen zaken opgehaald kunnen worden.';
        }

        $this->client = ContainerResolver::make()->getApiClient($attributes['zaakClient'] ?? 'openzaak');

        if (! $this->client->supports('zaken')) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        $zaken = get_transient($this->uniqueTransientKey($attributes));

        if ($zaken instanceof Collection && $zaken->isNotEmpty()) {
            return $this->returnView($attributes, $zaken);
        }

        if (! $attributes['combinedClients']) {
            $zaken = $this->getZaken($attributes);
        } else {
            $zaken = $this->getCombinedZaken($attributes);
        }

        if ($zaken->isEmpty()) {
            return 'Er zijn op dit moment geen zaken beschikbaar.';
        }

        set_transient($this->uniqueTransientKey($attributes), $zaken, 500);

        return $this->returnView($attributes, $zaken);
    }

    /**
     * Based on the configured attributes and the bsn of the current user.
     */
    protected function uniqueTransientKey(array $attributes): string
    {
        $attributes['bsnCurrentUser'] = $this->currentUserBsn;

        return md5(json_encode($attributes));
    }

    protected function getZaken(array $attributes): Collection
    {
        $filter = new ZakenFilter();
        $filter = $this->handleFilterBSN($filter, $attributes);
        $filter = $this->handleFilterZaaktype($filter, $attributes);

        $zaken = $this->client->zaken()->filter($filter);

        return $zaken->map(function ($zaak) {
            return $this->enrichZaak($zaak, $this->client);
        });
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
                $zaken[] = $client->zaken()->filter($filter)->map(function ($zaak) use ($client) {
                    return $this->enrichZaak($zaak, $this->client);
                })->all();
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

    /**
     * Set additional values to the 'Zaak'.
     * This way class methods, which are stored in the transient as well, can be used in the views.
     */
    protected function enrichZaak(Zaak $zaak, Client $client): Zaak
    {
        $zaak->setValue('leverancier', $client->getClientName());
        $zaak->setValue('steps', is_object($zaak->zaaktype) && $zaak->zaaktype->statustypen instanceof Collection ? $zaak->zaaktype->statustypen->sortByAttribute('volgnummer') : []);
        $zaak->setValue('status_history', $zaak->statussen);
        $zaak->setValue('information_objects', $zaak->zaakinformatieobjecten);
        $zaak->setValue('status_explanation', $zaak->status->statustoelichting);
        $zaak->setValue('result', $zaak->resultaat);

        return $zaak;
    }

    protected function handleFilterBSN(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! $attributes['byBSN']) {
            return $filter;
        }

        $filter->byBsn($this->currentUserBsn);

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

    protected function returnView(array $attributes, Collection $zaken)
    {
        if ('tabs' === $attributes['view']) {
            return view('blocks/mijn-zaken/overview-zaken-tabs.php', ['zaken' => $zaken]);
        }

        return view('blocks/mijn-zaken/overview-zaken.php', ['zaken' => $zaken]);
    }
}
