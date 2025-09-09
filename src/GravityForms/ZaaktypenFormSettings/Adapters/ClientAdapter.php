<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Adapters;

use Closure;
use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Informatieobjecttype;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\ClientInterface;
use OWC\Zaaksysteem\Support\Collection;

class ClientAdapter implements ClientInterface
{
    private Client $client;
    protected bool $isCron = false;
    protected int $timeout = 15;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Marks the request as being executed from a cron job.
     *
     * When enabled, cached values stored in transients will be bypassed,
     * but the cache will still be refreshed. This ensures that users
     * see up-to-date values the next time they open the form settings.
     */
    public function setIsCron(bool $isCron): self
    {
        $this->isCron = $isCron;

        return $this;
    }

    /**
     * Overrides the default request timeout.
     *
     * A longer timeout can be useful when running background tasks
     * such as cron jobs, where responses may take more time.
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Applies the configured timeout to the underlying client's request options.
     */
    public function applyTimeout(): void
    {
        $this->client
             ->getRequestClient()
             ->getRequestOptions()
             ->set('timeout', $this->timeout);
    }

    public function informatieobjecttypen(): array
    {
        try {
            return $this->getTypes(
                sprintf('%s-form-settings-information-object-type', $this->getClientNamePretty()), // Unique transient key.
                'informatieobjecttypen',
                function (Informatieobjecttype $objecttype) {
                    return [
                        'name' => $objecttype->url,
                        'label' => "{$objecttype->omschrijving} ({$objecttype->vertrouwelijkheidaanduiding})",
                        'value' => $objecttype->url,
                    ];
                },
                'No information object typen found.'
            );
        } catch (Exception $e) {
            return $this->handleNoChoices('informatieobjecttypen');
        }
    }

    public function zaaktypen(): array
    {
        try {
            return $this->getTypes(
                sprintf('%s-form-settings-zaaktypen', $this->getClientNamePretty()), // Unique transient key.
                'zaaktypen',
                function (Zaaktype $zaaktype) {
                    return [
                        'name' => $zaaktype->identificatie,
                        'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                        'value' => $zaaktype->url, // -> when the api supports filtering on zaaktype identification this line should be updated to $zaaktype->identificatie.
                    ];
                },
                'No zaaktypen found.'
            );
        } catch (Exception $e) {
            return $this->isCron ? [] : $this->handleNoChoices('zaaktypen');
        }
    }

    public function getClientNamePretty(): string
    {
        return $this->client->getClientNamePretty();
    }

    protected function getTypes(string $transientKey, string $endpoint, Closure $prepareCallback, string $emptyMessage): array
    {
        $types = get_transient($transientKey);

        if (is_array($types ?: false) && false === $this->isCron) {
            return $types;
        }

        $types = $this->fetchTypes($emptyMessage, $endpoint);
        $types = $this->prepareTypes($types, $prepareCallback);

        if (empty($types)) {
            return [];
        }

        set_transient($transientKey, $types, 64800); // 18 hours.

        return $types;
    }

    protected function fetchTypes(string $emptyMessage, string $endpoint): array
    {
        $page = 1;
        $types = [];
        $requestException = '';

        $this->applyTimeout();

        while ($page) {
            try {
                $result = $this->client->$endpoint()->all((new ResultaattypenFilter())->page($page));
            } catch (Exception $e) {
                $requestException = $e->getMessage();

                break;
            }

            $types = array_merge($types, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        $this->handleEmptyResult($types, $emptyMessage, $requestException);

        return $types;
    }

    protected function prepareTypes(array $types, Closure $prepareCallback): array
    {
        return (array) Collection::collect($types)->map($prepareCallback)->all();
    }

    protected function handleEmptyResult(array $types, string $emptyMessage, string $requestException): void
    {
        if (empty($types)) {
            $exceptionMessage = $emptyMessage;

            if (! empty($requestException)) {
                $exceptionMessage = sprintf('%s %s', $exceptionMessage, $requestException);
            }

            throw new Exception($exceptionMessage);
        }
    }

    protected function handleNoChoices(string $endpoint): array
    {
        return [
            [
                'label' => sprintf(__('Kan de "%s" die horen bij de geselecteerde leverancier niet ophalen.', 'owc-gravityforms-zaaksysteem'), $endpoint),
            ],
        ];
    }
}
