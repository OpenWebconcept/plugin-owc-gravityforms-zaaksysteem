<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Entities\Objectinformatie;
use OWC\Zaaksysteem\Support\PagedCollection;

class ObjectinformatieEndpoint extends Endpoint
{
    protected string $apiType = 'zaken';
    protected string $endpoint = 'objectinformatieobjecten';
    protected string $entityClass = Objectinformatie::class;

    public function all(): PagedCollection
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Objectinformatie
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    // public function filter(Filter\ZakenFilter $filter): PagedCollection
    // {
    //     $response = $this->client->get(
    //         $this->buildUri($this->endpoint, $filter),
    //         $this->buildRequestOptions($filter)
    //     );

    //     return $this->getPagedCollection($this->handleResponse($response));
    // }
}
