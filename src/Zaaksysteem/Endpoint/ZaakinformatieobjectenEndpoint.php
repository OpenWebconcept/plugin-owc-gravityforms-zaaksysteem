<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use OWC\Zaaksysteem\Support\PagedCollection;

class ZaakinformatieobjectenEndpoint extends Endpoint
{
    protected string $apiType = 'zaken';
    protected string $endpoint = 'zaakinformatieobjecten';
    protected string $entityClass = Zaakinformatieobject::class;

    // public function all(): PagedCollection
    // {
    //     This Endpoint has no all() method, as the zaak and
    //     informatieobject URI are mandatory parameters.
    // }

    public function get(string $identifier): ?Zaakinformatieobject
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZaakinformatieobjectenFilter $filter): PagedCollection
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
