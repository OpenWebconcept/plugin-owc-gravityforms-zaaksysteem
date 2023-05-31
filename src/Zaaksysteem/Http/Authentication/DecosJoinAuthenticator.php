<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Authentication;

use Firebase\JWT\JWT;

class DecosJoinAuthenticator extends TokenAuthenticator
{
    protected string $tokenUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct(string $tokenUrl, string $clientId, string $clientSecret)
    {
        $this->tokenUrl = $tokenUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function generateToken(): string
    {
        $payload = [
            'iss'                   => $this->clientId,
            'iat'                   => time(),
            'client_id'             => $this->clientId,
            'user_id'               => $this->clientId,
            'user_representation'   => $this->clientId,
        ];

        return JWT::encode($payload, $this->clientSecret, 'HS256');
    }
}
