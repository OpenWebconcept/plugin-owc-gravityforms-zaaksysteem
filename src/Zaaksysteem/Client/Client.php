<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Client;

use OWC\Zaaksysteem\Endpoint\Endpoint;
use OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator;
use OWC\Zaaksysteem\Http\Errors\ResourceNotFoundError;
use OWC\Zaaksysteem\Http\RequestClientInterface;

abstract class Client
{
    public const CLIENT_NAME = 'abstract';

    /**
     * This must be a callable abstract in the DI container.
     */
    public const CALLABLE_NAME = 'ab.client';
    public const AVAILABLE_ENDPOINTS = [];

    protected array $container = [];
    protected RequestClientInterface $client;
    protected TokenAuthenticator $authenticator;

    // Is new so we can pass this URL simply to the endpoint -> REFERENCE POINT: Mike
    protected string $endpointURL = '';

    // Does every API require token authentication? Maybe replace with interface
    public function __construct(RequestClientInterface $client, TokenAuthenticator $authenticator)
    {
        $this->client = $client;
        $this->authenticator = $authenticator;
    }

    public function __call($name, $arguments)
    {
        if (isset(static::AVAILABLE_ENDPOINTS[$name])) {
            return $this->fetchFromContainer($name);
        }

        throw new \BadMethodCallException("Unknown method {$name}");
    }

    public function getRequestClient(): RequestClientInterface
    {
        return $this->client;
    }

    public function getAuthenticator(): TokenAuthenticator
    {
        return $this->authenticator;
    }

    public function supports(string $endpoint): bool
    {
        return isset(static::AVAILABLE_ENDPOINTS[$endpoint]);
    }

    protected function getEndpointURL(): string
    {
        return $this->endpointURL;
    }

    public function setEndpointURL(string $url): self
    {
        if (empty($url)) {
            return $this;
        }

        $this->endpointURL = $url;

        return $this;
    }

    /**
     * @throws ResourceNotFoundError
     */
    protected function fetchFromContainer(string $key): Endpoint
    {
        // REFERENCE POINT: Mike -> catch and log or show critical error has occurred?
        if (empty($this->getEndpointURL())) {
            throw new ResourceNotFoundError(sprintf('Client "%s" must have an endpoint URL.', static::CLIENT_NAME));
        }

        if (! isset($this->container[$key]) || empty($this->container[$key])) {
            $class = static::AVAILABLE_ENDPOINTS[$key]; // Missing isset check
            $endpoint = new $class($this);
            $endpoint->setEndpointURL($this->getEndpointURL());
            $this->container[$key] = $endpoint;
        }

        return $this->container[$key];
    }
}
