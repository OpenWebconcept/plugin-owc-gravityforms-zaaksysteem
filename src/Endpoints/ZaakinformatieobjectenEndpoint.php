<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use OWC\Zaaksysteem\Support\Collection;

class ZaakinformatieobjectenEndpoint extends Endpoint
{
    protected string $endpoint = 'zaakinformatieobjecten';
    protected string $entityClass = Zaakinformatieobject::class;

    public function all(): Collection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Zaakinformatieobject
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function create(Zaakinformatieobject $model): Zaakinformatieobject
    {
        $response = $this->httpClient->post(
            $this->buildUri($this->endpoint),
            $model->prepareCreateJsonArgs(),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZaakinformatieobjectenFilter $filter): Collection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getCollection($this->handleResponse($response));
    }
}
