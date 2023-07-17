<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use OWC\Zaaksysteem\Endpoints\Endpoint;
use OWC\Zaaksysteem\Http\RequestClientInterface;

abstract class AbstractClient implements Client
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

    protected function fetchFromContainer(string $key): Endpoint
    {
        if (! isset($this->container[$key]) || empty($this->container[$key])) {
            $class = static::AVAILABLE_ENDPOINTS[$key]; // Missing isset check
            $endpoint = new $class($this);
            $this->container[$key] = $endpoint;
        }

        return $this->container[$key];
    }
}