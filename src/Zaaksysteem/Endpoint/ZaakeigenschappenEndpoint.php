<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;

class ZaakeigenschappenEndpoint extends Endpoint
{
    protected string $apiType = 'zaken';
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

    public function create($uuid, Zaakeigenschap $model): Zaakeigenschap
    {
        $response = $this->httpClient->post(
            $this->buildUri($this->apiType . '/' . $uuid . '/' . $this->endpoint),
            $model->toJson(),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }
}
