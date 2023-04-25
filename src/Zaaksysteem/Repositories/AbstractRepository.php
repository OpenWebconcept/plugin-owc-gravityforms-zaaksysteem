<?php

namespace OWC\Zaaksysteem\Repositories;

abstract class AbstractRepository
{
    public function request(string $url = '', string $method = 'GET', array $args = [], $test = ''): array
    {
        if (empty($url)) {
            return [];
        }

        $this->shouldDisableSSL();

        $request = \wp_remote_request($url, $this->getRequestArgs($method, $args));
        $httpSuccessCodes = [200, 201];

        // if($test === 'hoi') {
        //     echo(json_decode($request['body'], true)['detail']);
        //     die;
        // }
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

    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    public function mapArgs(array $args, array $fields, array $entry): array
    {
        foreach ($fields as $field) {
            if (empty($field->linkedFieldValueZGW) || ! isset($args[$field->linkedFieldValueZGW])) {
                continue;
            }

            $property = rgar($entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ($field->type === 'date') {
                $property = (new \DateTime($property))->format('Y-m-d');
            }

            $args[$field->linkedFieldValueZGW] = $property;
        }

        return $args;
    }
}
