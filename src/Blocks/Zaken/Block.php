<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Support\Collection;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class Block
{
    public function render($attributes, $rendered, $editor)
    {
        $client = $this->getApiClient($attributes);

        if (! $client->supports('zaken')) {
            return 'Het Mijn Zaken overzicht is niet beschikbaar.';
        }

        $zaken = $this->getZaken($client);

        if ($zaken->isEmpty()) {
            return 'Er zijn op dit moment geen zaken beschikbaar.';
        }

        return view('blocks/mijn-zaken/overview-zaken.php', ['zaken' => $zaken]);
    }

    protected function getApiClient(array $attributes): Client
    {
        $client = $attributes['zaakClient'] ?? 'openzaak';

        switch ($client) {
            case 'decosjoin':
                $client = resolve('dj.client');
                $secret = resolve('dj.client_secret_zrc');
                $client->getAuthenticator()->setClientSecret($secret);

                return $client;
            case 'openzaak': // fallthrough.
            default:
                return resolve('oz.client');
        }
    }

    protected function getZaken(Client $client): Collection
    {
        $filter = new ZakenFilter();
        $filter->add('identificatie', 'ZAAK-2023-0000000064');
        // $filter->add('zaaktype', 'https://open-zaak.test.buren.opengem.nl/catalogi/api/v1/zaaktypen/070e9270-339d-4448-8fb4-e2fc9d38d59d');

        // https://open-zaak.test.buren.opengem.nl/catalogi/api/v1/zaaktypen/070e9270-339d-4448-8fb4-e2fc9d38d59d

        return $client->zaken()->filter($filter);
        // return $client->zaken()->all(); // Decos
    }
}
