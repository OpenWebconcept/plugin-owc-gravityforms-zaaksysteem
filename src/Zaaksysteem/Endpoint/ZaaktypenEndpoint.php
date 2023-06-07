<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Support\PagedCollection;

class ZaaktypenEndpoint extends Endpoint
{
    protected string $apiType = 'catalogi'; // REFERENCE POINT: Mike kan weg, zo ja dan bij alle endpoint classes?
    protected string $endpoint = 'zaaktypen';
    protected string $entityClass = Zaaktype::class;

    public function all(?Filter\AbstractFilter $filter = null): PagedCollection
    {
        $response = $this->httpClient->get(
            $filter ? $this->buildUri($this->endpoint, $filter) : $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Zaaktype
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZaaktypenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
