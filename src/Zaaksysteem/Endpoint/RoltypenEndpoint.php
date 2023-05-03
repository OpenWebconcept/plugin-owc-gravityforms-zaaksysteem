<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Entities\Roltype;
use OWC\Zaaksysteem\Support\PagedCollection;

class RoltypenEndpoint extends Endpoint
{
    protected string $apiType = 'catalogi';
    protected string $endpoint = 'roltypen';
    protected string $entityClass = Roltype::class;

    public function all(string $params): PagedCollection
    {
        if ($params) {
            // var_dump($this->buildUri($this->endpoint . '?' . $params));
            // die;
            $response = $this->httpClient->get(
                $this->buildUri($this->endpoint . '?' . $params),
                $this->buildRequestOptions()
            );
        } else {
            $response = $this->httpClient->get(
                $this->buildUri($this->endpoint),
                $this->buildRequestOptions()
            );
        }

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
