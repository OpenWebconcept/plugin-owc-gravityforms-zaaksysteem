<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Authentication;

use OWC\Zaaksysteem\Http\RequestClientInterface;

class DecosJoinAuthenticator extends TokenAuthenticator
{
    protected RequestClientInterface $client;
    protected string $tokenUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct(RequestClientInterface $client, string $tokenUrl, string $clientId, string $clientSecret)
    {
        $this->client = $client;
        $this->tokenUrl = $tokenUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function generateToken(): string
    {
        $response = $this->client->post($this->tokenUrl, [
            'clientId'          => $this->clientId,
            'clientSecret'      => $this->clientSecret,
            'expiresInMinute'   => '2'
        ])->getParsedJson();

        // Does this return some sort of lifetime data? Because that
        // would allow to cache the token for a short period.
        return $response['token'] ?? '';
    }
}
