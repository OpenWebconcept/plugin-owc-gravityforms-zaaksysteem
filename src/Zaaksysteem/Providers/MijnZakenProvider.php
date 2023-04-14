<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Providers;

use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\Endpoint\Filter\ZakenFilter;

use function Yard\DigiD\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;
use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;

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
                'title'         => 'Mijn Zaken',
                'style'             => 'swf-extended-blocks-style',
                'editor_script'     => 'swf-extended-blocks-script',
                'editor_style'      => 'swf-extended-blocks-editor-style',
            ]
        ]);
    }

    public function renderBlock($attributes, $rendered): string
    {
        $client = $this->plugin->getContainer()->get('api.client');
        if ($client->supports('zaken') === false) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        $filter = new ZakenFilter();
        $filter->byBsn($this->resolveCurrentBsn());

        $zaaktypeUris = $this->getFilterableZaaktypeUris($attributes);

        // If there's one zaaktype URIs we can add it to the query.
        if (count($zaaktypeUris) === 1) {
            $filter->add('zaaktype', reset($zaaktypeUris));
        }

        $zaken = $client->zaken()->filter($filter);

        // If there were multiple zaaktype URIs, we have to do some
        // post-processing and filter them out from the collection.
        if (count($zaaktypeUris) > 1) {
            $zaken = $zaken->filter(function (Zaak $zaak) use ($zaaktypeUris) {
                return in_array($zaak->getAttributeValue('zaaktype'), $zaaktypeUris);
            });
        }

        if ($zaken->isEmpty()) {
            return 'Er zijn op dit moment geen zaken beschikbaar.';
        }

        return view('mijn-zaken/overview-zaken.php', compact('zaken'));
    }

    protected function getFilterableZaaktypeUris(array $attributes): array
    {
        $uris = json_decode($attributes['zaaktypeFilter'] ?? '', true);

        return array_filter((array) $uris, function ($uri) {
            return filter_var($uri, FILTER_VALIDATE_URL);
        });
    }

    /**
     * @todo move this to separate handler
     */
    protected function resolveCurrentBsn(): string
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn');

        return decrypt($bsn);
    }
}
