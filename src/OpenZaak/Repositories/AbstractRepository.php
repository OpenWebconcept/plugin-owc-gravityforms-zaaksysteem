<?php

namespace OWC\OpenZaak\Repositories;

abstract class AbstractRepository
{
    public function request(string $url = '', string $method = 'GET', array $args = []): array
    {
        if (empty($url)) {
            return [];
        }

        $request = \wp_remote_request($url, $this->getRequestArgs($method, $args)); // andere manier?

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

    abstract protected function getRequestArgs(string $method, array $args = []): array;
}
