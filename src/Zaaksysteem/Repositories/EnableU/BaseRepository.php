<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU;

use OWC\Zaaksysteem\GravityForms\GravityFormsSettings;

use OWC\Zaaksysteem\Repositories\AbstractRepository;

class BaseRepository extends AbstractRepository
{
    protected string $baseURL;

    public function __construct()
    {
        $this->baseURL = GravityFormsSettings::make()->get('-enable-u-url');
    }

    protected function getRequestArgs(string $method, array $args = []): array
    {
        $requestArgs = [
            'method' => $method,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 60,
        ];

        if ('POST' === $method && ! empty($args)) {
            $requestArgs = array_merge($requestArgs, ['body' => json_encode($args, JSON_UNESCAPED_SLASHES)]);
        }

        return $requestArgs;
    }

    protected function makeURL(string $uri = ''): string
    {
        if (empty($uri)) {
            return $this->baseURL;
        }
        
        return sprintf('%s/%s', $this->baseURL, $uri);
    }
}
