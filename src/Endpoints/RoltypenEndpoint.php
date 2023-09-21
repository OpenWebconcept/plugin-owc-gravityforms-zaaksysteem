<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Roltype;
use OWC\Zaaksysteem\Support\PagedCollection;

class RoltypenEndpoint extends Endpoint
{
    protected string $endpoint = 'roltypen';
    protected string $entityClass = Roltype::class;

    public function all(): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Roltype
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\RoltypenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
