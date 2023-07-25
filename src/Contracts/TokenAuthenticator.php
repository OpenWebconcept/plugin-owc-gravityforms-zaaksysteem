<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

interface TokenAuthenticator
{
    public function generateToken(): string;
    public function getAuthString(): string;
}
