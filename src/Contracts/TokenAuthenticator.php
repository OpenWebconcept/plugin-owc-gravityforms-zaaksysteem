<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

interface TokenAuthenticator
{
    public function generateToken(): string;
    public function setClientSecret(string $clientSecret): self; // Is not needed per se, could be removed after PR review.
    public function getAuthString(): string;
}
