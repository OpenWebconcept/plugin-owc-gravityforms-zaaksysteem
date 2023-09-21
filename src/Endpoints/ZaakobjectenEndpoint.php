<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Zaakobject;
use OWC\Zaaksysteem\Support\PagedCollection;

class ZaakobjectenEndpoint extends Endpoint
{
    protected string $endpoint = 'zaakobjecten';
    protected string $entityClass = Zaakobject::class;

    public function all(): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Zaakobject
    {
        $response = $this->httpClient->get(
            $this->buildUri(sprintf('%s/%s', $this->endpoint, $identifier)),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZaakobjectenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
