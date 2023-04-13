<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Entities\Catalogus;
use OWC\Zaaksysteem\Support\PagedCollection;

class CatalogussenEndpoint extends Endpoint
{
    protected string $apiType = 'catalogi';
    protected string $endpoint = 'catalogus';
    protected string $entityClass = Catalogus::class;

    public function all(): PagedCollection
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Catalogus
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\CatalogusFilter $filter): PagedCollection
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
