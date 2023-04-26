<?php

namespace OWC\Zaaksysteem\Repositories;

abstract class AbstractRepository
{
    public function request(string $url = '', string $method = 'GET', array $args = []): array
    {
        if (empty($url)) {
            return [];
        }

        $this->shouldDisableSSL();

        $request = \wp_remote_request($url, $this->getRequestArgs($method, $args));
        $httpSuccessCodes = [200, 201];

        if (\is_wp_error($request) || ! in_array($request['response']['code'], $httpSuccessCodes)) {
            return [];
        }

        $body = json_decode($request['body'], true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($body)) {
            return [];
        }

        return is_array($body) && ! empty($body) ? $body : [];
    }

    /**
     * Disable sslverify on all environments except on production.
     */
    protected function shouldDisableSSL(): void
    {
        $environment = $_ENV['APP_ENV'] ?? 'production';

        if ($environment === 'production') {
            return;
        }
        
        \add_filter('http_request_args', function ($args, $url) {
            $args['sslverify'] = false;
            
            return $args;
        }, 10, 2);
    }

    abstract protected function getRequestArgs(string $method, array $args = []): array;
}
