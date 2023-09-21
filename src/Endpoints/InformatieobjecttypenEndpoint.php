<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Informatieobjecttype;
use OWC\Zaaksysteem\Support\PagedCollection;

class InformatieobjecttypenEndpoint extends Endpoint
{
    protected string $endpoint = 'informatieobjecttypen';
    protected string $entityClass = Informatieobjecttype::class;

    public function all(): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Informatieobjecttype
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\InformatieobjecttypenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
