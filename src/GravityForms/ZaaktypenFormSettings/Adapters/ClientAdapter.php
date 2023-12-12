<?php

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Adapters;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\ClientInterface;
use OWC\Zaaksysteem\Support\Collection;

class ClientAdapter implements ClientInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function zaaktypen(): array
    {
        try {
            return $this->getTypesByClient($this->client);
        } catch(Exception $e) {
            return $this->handleNoChoices();
        }
    }

    public function getClientNamePretty(): string
    {
        return $this->client->getClientNamePretty();
    }

    /**
     * Return types by client, includes pagination.
     */
    protected function getTypesByClient(Client $client): array
    {
        $transientKey = sprintf('%s-form-settings-zaaktypen', $client->getClientNamePretty());
        $types = get_transient($transientKey);

        if (is_array($types) && $types) {
            return $types;
        }

        $zaaktypen = $this->getTypes($client);
        $types = $this->prepareTypes($zaaktypen);

        set_transient($transientKey, $types, 500);

        return $types;
    }

    protected function getTypes(Client $client): array
    {
        $page = 1;
        $zaaktypen = [];
        $requestException = '';

        while ($page) {
            try {
                $result = $client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
            } catch (Exception $e) {
                $requestException = $e->getMessage();

                break;
            }

            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        if (empty($zaaktypen)) {
            $exceptionMessage = 'No zaaktypen found.';

            if (! empty($requestException)) {
                $exceptionMessage = sprintf('%s %s', $exceptionMessage, $requestException);
            }

            throw new Exception($exceptionMessage);
        }

        return $zaaktypen;
    }

    protected function prepareTypes(array $zaaktypen): array
    {
        return (array) Collection::collect($zaaktypen)->map(function (Zaaktype $zaaktype) {
            return [
                'name' => $zaaktype->identificatie,
                'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                'value' => $zaaktype->identificatie,
            ];
        })->all();
    }

    protected function handleNoChoices(): array
    {
        return [
            [
                'label' => __('Unable to retrieve "Zaak" types provided by selected supplier.', 'owc-gravityforms-zaaksysteem'),
            ],
        ];
    }
}
