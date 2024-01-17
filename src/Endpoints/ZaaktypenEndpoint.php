<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use Exception;
use OWC\Zaaksysteem\Endpoints\Filter\AbstractFilter;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Support\PagedCollection;

class ZaaktypenEndpoint extends Endpoint
{
    protected string $endpoint = 'zaaktypen';
    protected string $entityClass = Zaaktype::class;

    public function all(?AbstractFilter $filter = null): PagedCollection
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
            $this->buildUri(sprintf('%s/%s', $this->endpoint, $identifier)),
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

    public function byIdentifier(string $zaaktypeIdentifier): ?Zaaktype
    {
        $page = 1;
        $zaaktypen = [];

        while ($page) {
            try {
                $result = $this->all((new ResultaattypenFilter())->page($page));
            } catch (Exception $e) {
                break;
            }

            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        return Collection::collect($zaaktypen)->filter(
            function (Zaaktype $zaaktype) use ($zaaktypeIdentifier) {
                if ($zaaktype->identificatie === $zaaktypeIdentifier) {
                    return $zaaktype;
                }
            }
        )->first() ?: null;
    }
}
