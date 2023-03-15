<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Repositories;

use Firebase\JWT\JWT;
use OWC\OpenZaak\GravityForms\GravityFormsSettings;

abstract class BaseRepository
{
    public function __construct()
    {
        $this->baseURL = GravityFormsSettings::make()->get('sp-url');
        $this->clientID = GravityFormsSettings::make()->get('sp-client-id');
        $this->clientSecret = GravityFormsSettings::make()->get('sp-client-secret');
    }

    public function request(string $url = '', string $method = 'GET', array $args = []): array
    {
        if (empty($url)) {
            return [];
        }

        $request = \wp_remote_request($url, $this->getRequestArgs($method, $args));

        $httpSuccessCodes = [200, 201];

        if (\is_wp_error($request) || !in_array($request['response']['code'], $httpSuccessCodes)) {
            return [];
        }

        $body = json_decode($request['body'], true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($body)) {
            return [];
        }

        return is_array($body) && !empty($body) ? $body : [];
    }

    protected function getRequestArgs(string $method, array $args = [])
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


        if ('POST' === $method && !empty($args)) {
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
