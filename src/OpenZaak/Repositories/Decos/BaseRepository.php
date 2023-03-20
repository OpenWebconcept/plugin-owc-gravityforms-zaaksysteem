<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Repositories\Decos;

use OWC\OpenZaak\GravityForms\GravityFormsSettings;
use OWC\OpenZaak\Repositories\AbstractRepository;

class BaseRepository extends AbstractRepository
{
    protected string $baseURL;
    protected string $tokenURL;
    protected string $clientID;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseURL = GravityFormsSettings::make()->get('decos-url');
        $this->tokenURL = GravityFormsSettings::make()->get('decos-token-url');
        $this->clientID = GravityFormsSettings::make()->get('decos-client-id');
        $this->clientSecret = GravityFormsSettings::make()->get('decos-client-secret');
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
        $args = [
            'clientId' => $this->clientID,
            'clientSecret' => $this->clientSecret,
            'expiresInMinute' => '2'
        ];

        $response = $this->request($this->tokenURL, 'POST', $args); // TODO: calls a loop
        var_dump($response);
        die;

        return $response['token'] ?? '';
    }
}
