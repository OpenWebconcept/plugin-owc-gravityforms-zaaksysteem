<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Authentication;

use Firebase\JWT\JWT;

class RoxitAuthenticator extends TokenAuthenticator
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
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
