<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Adapters;

use Closure;
use Exception;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\ClientInterface;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Services\TypeRetrievalService;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Support\TypeCache;

class ClientAdapter implements ClientInterface
{
    private string $clientNamePretty;
    private TypeRetrievalService $fetcher;
    private TypeCache $cache;
    protected bool $isCron = false;
    protected int $timeout = 15;

    public function __construct(string $clientNamePretty, TypeRetrievalService $fetcher, TypeCache $cache)
    {
        $this->clientNamePretty = $clientNamePretty;
        $this->fetcher = $fetcher;
        $this->cache = $cache;
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

    public function informatieobjecttypen(): array
    {
        return $this->getTypes(
            'informatieobjecttypen',
            fn ($type) => [
                'name' => $type->url,
                'label' => "{$type->omschrijving} ({$type->vertrouwelijkheidaanduiding})",
                'value' => $type->url,
            ]
        );
    }

    public function zaaktypen(): array
    {
        return $this->getTypes(
            'zaaktypen',
            fn ($type) => [
                'name' => $type->identificatie,
                'label' => "{$type->omschrijving} ({$type->identificatie})",
                'value' => $type->url,
            ]
        );
    }

    protected function getTypes(string $endpoint, Closure $prepare): array
    {
        $key = sprintf('%s-form-settings-%s', $this->clientNamePretty, $endpoint);

        if (! $this->isCron && ($cached = $this->cache->get($key))) {
            return $cached;
        }

        try {
            $types = $this->fetcher->fetch($endpoint, "No $endpoint found.");
            $data = array_map($prepare, $types);

            $this->cache->put($key, $data);

            return $data;
        } catch (Exception $e) {
            return [['label' => sprintf(__('Kan de "%s" niet ophalen.', 'owc-gravityforms-zaaksysteem'), $endpoint)]];
        }
    }
}
