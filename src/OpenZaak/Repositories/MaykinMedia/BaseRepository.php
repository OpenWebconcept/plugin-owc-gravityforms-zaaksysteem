<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Repositories\MaykinMedia;

use Firebase\JWT\JWT;
use OWC\OpenZaak\GravityForms\GravityFormsSettings;
use OWC\OpenZaak\Repositories\AbstractRepository;

class BaseRepository extends AbstractRepository
{
    protected string $baseURL;

    protected string $clientID;

    protected string $clientSecret;

    public function __construct()
    {
        $this->baseURL = GravityFormsSettings::make()->get('maykin-url');
        $this->clientID = GravityFormsSettings::make()->get('maykin-client-id');
        $this->clientSecret = GravityFormsSettings::make()->get('maykin-client-secret');
    }

    protected function getRequestArgs(string $method, array $args = []): array
    {
        $requestArgs = [
            'method' => $method,
            'headers' => [
                'Accept-Crs' => 'EPSG:4326',
                'Content-Crs' => 'EPSG:4326',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $this->generateToken())
            ]
        ];

        if ('POST' === $method && ! empty($args)) {
            $requestArgs = array_merge($requestArgs, ['body' => json_encode($args, JSON_UNESCAPED_SLASHES)]);
        }

        return $requestArgs;
    }

    protected function makeURL(string $uri = ''): string
    {
        return sprintf('%s/%s', $this->baseURL, $uri);
    }

    protected function generateToken(): string
    {
        $payload = [
            'iss' => $this->clientID,
            'iat' => time(),
            'client_id' => $this->clientID,
            'user_id' => $this->clientID,
            'user_representation' => $this->clientID,
        ];

        return JWT::encode($payload, $this->clientSecret, 'HS256');
    }
}
