<?php

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Http\PageMeta;
use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Http\Handlers\Stack;
use OWC\Zaaksysteem\Http\RequestOptions;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator;

abstract class Endpoint
{
    protected RequestClientInterface $client;
    protected TokenAuthenticator $authenticator;
    protected Stack $responseHandlers;

    protected string $apiType = 'api-type'; // E.g. 'zaken' or 'catalogi'
    protected string $version = 'v1';
    protected string $endpoint = 'endpoint';
    protected string $entityClass = Entity::class;

    public function __construct(RequestClientInterface $client, TokenAuthenticator $authenticator)
    {
        $this->client = $client;
        $this->authenticator = $authenticator;
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
            'headers'   => [
                'Authorization'     => $this->authenticator->getAuthString()
            ],
        ]);
    }

    protected function buildUri(string $uri, ?Filter\AbstractFilter $filter = null): string
    {
        $uri = sprintf('%s/api/%s/%s', $this->apiType, $this->version, $uri);

        if ($filter) {
            $uri = \add_query_arg($filter->getParameters(), $uri);
        }

        return $uri;
    }

    public function getSingleEntity(Response $response): Entity
    {
        return new $this->entityClass($response->getParsedJson());
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
            return new $this->entityClass($item);
        }, $data);
    }
}
