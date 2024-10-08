<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Http\PageMeta;
use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Http\Handlers\Stack;
use OWC\Zaaksysteem\Http\RequestOptions;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Contracts\TokenAuthenticator;
use OWC\Zaaksysteem\Endpoints\Traits\SupportsExpand;

abstract class Endpoint
{
    use SupportsExpand;

    protected Client $client;
    protected string $endpointURL = '';
    protected RequestClientInterface $httpClient;
    protected TokenAuthenticator $authenticator;
    protected Stack $responseHandlers;

    protected string $entityClass = Entity::class;

    public function __construct(Client $client, string $endpointURL)
    {
        $this->client = $client;
        $this->endpointURL = $endpointURL;
        $this->httpClient = $client->getRequestClient();
        $this->authenticator = $client->getAuthenticator();
        $this->responseHandlers = Stack::create();
    }

    protected function handleResponse(Response $response)
    {
        foreach ($this->responseHandlers->get() as $handler) {
            $response = $handler->handle($response);
        }

        return $response;
    }

    protected function buildRequestOptions(): RequestOptions
    {
        return new RequestOptions([
            'headers' => [
                'Authorization' => $this->authenticator->getAuthString(),
            ],
        ]);
    }

    protected function buildUri(string $uri, ?Filter\AbstractFilter $filter = null): string
    {
        $uri = sprintf('%s/%s', untrailingslashit($this->endpointURL), $uri);

        if ($filter) {
            $uri = \add_query_arg($filter->getParameters(), $uri);
        }

        return $uri;
    }

    protected function buildUriWithExpand(string $uri, ?Filter\AbstractFilter $filter = null): string
    {
        $uri = $this->buildUri($uri, $filter);

        if ($this->endpointSupportsExpand() && $this->expandIsEnabled()) {
            $uri = add_query_arg([
                'expand' => implode(',', $this->getExpandableResources())
            ], $uri);
        }

        return $uri;
    }

    public function getSingleEntity(Response $response): Entity
    {
        return $this->buildEntity($response->getParsedJson());
    }

    protected function getPagedCollection(Response $response): PagedCollection
    {
        $data = $response->getParsedJson();

        return new PagedCollection(
            $this->mapEntities($data['results'] ?? []),
            PageMeta::fromResponse($response)
        );
    }

    protected function getCollection(Response $response): Collection
    {
        return new Collection(
            $this->mapEntities($response->getParsedJson()),
            PageMeta::fromResponse($response)
        );
    }

    protected function mapEntities(array $data): array
    {
        return array_map(function ($item) {
            return $this->buildEntity($item);
        }, $data);
    }

    protected function buildEntity($data): Entity
    {
        $class = $this->entityClass;

        return new $class($data, $this->client::CALLABLE_NAME, $this->client::CLIENT_NAME);
    }
}
