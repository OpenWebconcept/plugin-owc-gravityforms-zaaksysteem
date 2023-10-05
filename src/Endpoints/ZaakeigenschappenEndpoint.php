<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Support\Collection;

class ZaakeigenschappenEndpoint extends Endpoint
{
    protected string $endpoint = 'zaakeigenschappen';
    protected string $entityClass = Zaakeigenschap::class;

    public function all(): Collection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Zaakeigenschap
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZaakeigenschappenFilter $filter): Collection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getCollection($this->handleResponse($response));
    }

    public function create(Zaak $zaak, Zaakeigenschap $model): Zaakeigenschap
    {
        $response = $this->httpClient->post(
            $this->buildUri(sprintf('zaken/%s/%s', $zaak->uuid, $this->endpoint)),
            $model->toJson(),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }
}
