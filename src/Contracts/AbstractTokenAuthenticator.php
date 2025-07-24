<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

abstract class AbstractTokenAuthenticator implements TokenAuthenticator
{
    protected string $clientSecret;

    abstract public function generateToken(): string;

    /**
     * Some implementations may require multiple client secrets based on the API used.
     * This method allows setting a client secret dynamically.
     */
    public function setClientSecret(string $clientSecret): self
    {
        if ('' === trim($clientSecret)) {
            return $this;
        }

        $this->clientSecret = trim($clientSecret);

        return $this;
    }

    public function getAuthString(): string
    {
        return sprintf('Bearer %s', $this->generateToken());
    }
}
