<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Providers;

use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Foundation\ServiceProvider;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class MijnZakenProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerBlock']);
    }

    public function registerBlock()
    {
        register_block_type('owc/mijn-zaken', [
            'render_callback' => [$this, 'renderBlock'],
            'attributes'      => [
                'zaakClient' => 'openzaak',
                'title' => 'Mijn Zaken',
                'style' => 'swf-extended-blocks-style',
                'editor_script' => 'swf-extended-blocks-script',
                'editor_style' => 'swf-extended-blocks-editor-style',
            ]
        ]);
    }

    public function renderBlock($attributes, $rendered): string
    {
        $client = $this->getApiClient($attributes);
        if ($client->supports('zaken') === false) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        $currentBsn = resolve('digid.current_user_bsn');
        $filter = new ZakenFilter();
        // $filter->byBsn($currentBsn);
        $filter->add('identificatie', 'ZAAK-2023-0000000064');

        $zaken = $client->zaken()->filter($filter);
        // $zaken = $client->zaken()->all();

        // Filter down the list of zaken by checking if the linked Zaaktype
        // has an identifier that is within a set of allowable identifiers.
        // $zaaktypeIdentifiers = $this->getFilterableZaaktypeIdentifiers($attributes);
        // if (! empty($zaaktypeIdentifiers)) {
        //     $zaken = $zaken->filter(function (Zaak $zaak) use ($zaaktypeIdentifiers) {
        //         return in_array($zaak->zaaktype->identificatie, $zaaktypeIdentifiers);
        //     });
        // }

        // Make sure we display zaken that are initiated by the current user,
        // as opposed to zaken about the current user. We'll do this after
        // filtering zaaktypes as this action initiates an additional HTTP request.
        // $zaken = $zaken->filter(function (Zaak $zaak) use ($currentBsn) {
        //     return $zaak->isInitiatedBy($currentBsn);
        // });

        if ($zaken->isEmpty()) {
            return 'Er zijn op dit moment geen zaken beschikbaar.';
        }

        return view('blocks/mijn-zaken/overview-zaken.php', compact('zaken'));
    }

    protected function getApiClient(array $attributes): Client
    {
        $client = $attributes['zaakClient'] ?? 'openzaak';

        switch ($client) {
            case 'decosjoin':
                return $this->plugin->getContainer()->get('dj.client');
            case 'openzaak': //fallthrough
            default:
                return $this->plugin->getContainer()->get('oz.client');
        }
    }

    protected function getFilterableZaaktypeIdentifiers(array $attributes): array
    {
        $uris = json_decode($attributes['zaaktypeFilter'] ?? '', true);

        return array_filter((array) $uris);
    }
}
