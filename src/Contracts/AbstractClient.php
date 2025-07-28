<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use InvalidArgumentException;
use OWC\Zaaksysteem\Endpoints\Endpoint;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use OWC\Zaaksysteem\Http\Errors\ResourceNotFoundError;
use OWC\Zaaksysteem\Http\Errors\ServerError;
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

    protected string $zakenEndpointUrl;
    protected string $catalogiEndpointUrl;
    protected string $documentenEndpointUrl;

    // Does every API require token authentication? Maybe replace with interface
    public function __construct(
        RequestClientInterface $client,
        TokenAuthenticator $authenticator,
        string $zakenEndpointUrl,
        string $catalogiEndpointUrl,
        string $documentenEndpointUrl
    ) {
        $this->client = $client;
        $this->authenticator = $authenticator;
        $this->zakenEndpointUrl = $zakenEndpointUrl;
        $this->catalogiEndpointUrl = $catalogiEndpointUrl;
        $this->documentenEndpointUrl = $documentenEndpointUrl;
    }

    public function __call($name, $arguments)
    {
        if (isset(static::AVAILABLE_ENDPOINTS[$name])) {
            return $this->fetchFromContainer($name);
        }

        throw new \BadMethodCallException("Unknown method {$name}");
    }

    public function getClientName(): string
    {
        return static::CALLABLE_NAME;
    }

    public function getClientNamePretty(): string
    {
        return static::CLIENT_NAME;
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
            $endpoint = $this->validateEndpoint($key); // Throws exception when validation fails.

            [$class, $type] = $endpoint;

            $this->setClientSecretByType($type);

            $endpoint = new $class($this, $this->getEndpointUrlByType($type));
            $this->container[$key] = $endpoint;
        }

        return $this->container[$key];
    }

    protected function validateEndpoint(string $key): array
    {
        $endpoint = static::AVAILABLE_ENDPOINTS[$key] ?? false;

        if (! $endpoint) {
            throw new ResourceNotFoundError(sprintf('Available endpoint lookup of client "%s" failed. Endpoint defined by key "%s" does not exists.', static::CLIENT_NAME, $key));
        }

        [$class, $type] = $endpoint;

        if (! class_exists($class)) {
            throw new ServerError(sprintf('Available endpoint lookup of client "%s" failed. Class defined by key "%s" does not exists.', static::CLIENT_NAME, $key));
        }

        if (empty($type)) {
            throw new ServerError(sprintf('Available endpoint lookup of client "%s" failed. Defined class "%s" does not have a endpoint type defined.', static::CLIENT_NAME, $class));
        }

        return $endpoint;
    }

    /**
     * Applies only to Decos.
     */
    protected function setClientSecretByType(string $type): self
    {
        if ($this->getClientNamePretty() !== 'decos-join') {
            return $this;
        }

        if ('zaken' === $type || 'documenten' === $type) {
            $secret = resolve('dj.client_secret_zrc');
        } else {
            $secret = resolve('dj.client_secret');
        }

        $this->getAuthenticator()->setClientSecret($secret);

        return $this;
    }

    public function getEndpointUrlByType(string $type): string
    {
        switch ($type) {
            case 'zaken':
                return $this->zakenEndpointUrl;
            case 'catalogi':
                return $this->catalogiEndpointUrl;
            case 'documenten':
                return $this->documentenEndpointUrl;
            default:
                throw new InvalidArgumentException("Unknown endpoint type {$type}");
        }
    }
}
