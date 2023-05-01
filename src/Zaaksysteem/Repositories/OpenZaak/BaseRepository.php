<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\OpenZaak;

use Firebase\JWT\JWT;
use OWC\Zaaksysteem\GravityForms\GravityFormsSettings;
use OWC\Zaaksysteem\Repositories\AbstractRepository;

class BaseRepository extends AbstractRepository
{
    protected string $baseURL;

    protected string $clientID;

    protected string $clientSecret;

    /**
     * Construction of the base repository.
     */
    public function __construct()
    {
        $this->baseURL = GravityFormsSettings::make()->get('-openzaak-url');
        $this->clientID = GravityFormsSettings::make()->get('-openzaak-client-id');
        $this->clientSecret = GravityFormsSettings::make()->get('-openzaak-client-secret');
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
