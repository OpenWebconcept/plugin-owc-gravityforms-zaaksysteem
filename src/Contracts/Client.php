<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use OWC\Zaaksysteem\Http\RequestClientInterface;

interface Client
{
    public function __construct(RequestClientInterface $client, TokenAuthenticator $authenticator);
    public function __call($name, $arguments);
    public function getRequestClient(): RequestClientInterface;
    public function getAuthenticator(): TokenAuthenticator;
    public function supports(string $endpoint): bool;
}
