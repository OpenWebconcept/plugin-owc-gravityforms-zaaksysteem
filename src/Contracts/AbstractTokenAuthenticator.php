<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

abstract class AbstractTokenAuthenticator implements TokenAuthenticator
{
    abstract public function generateToken(): string;

    public function getAuthString(): string
    {
        return sprintf('Bearer %s', $this->generateToken());
    }
}
