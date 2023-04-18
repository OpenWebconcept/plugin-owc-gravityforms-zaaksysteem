<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Client;

use OWC\Zaaksysteem\Endpoint\Endpoint;
use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator;

abstract class Client
{
    public const CLIENT_NAME = 'abstract';
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
