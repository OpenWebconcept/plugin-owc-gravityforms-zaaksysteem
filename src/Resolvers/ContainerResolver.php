<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use DI\Container;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Foundation\Plugin;

class ContainerResolver
{
    protected Container $container;

    final private function __construct()
    {
        $this->container = Plugin::getInstance()->getContainer();
    }

    public static function make(): self
    {
        return new static();
    }

    public function get(string $key)
    {
        return $this->container->get($key) ?? null;
    }

    public function getApiClient(string $client): Client
    {
        switch ($client) {
            case 'decos':
            case 'decos-join':
                return $this->container->get('dj.client');
            case 'rx-mission':
                return $this->container->get('rx.client');
            case 'xxllnc':
                return $this->container->get('xxllnc.client');
            case 'openzaak': // fallthrough.
            default:
                return $this->container->get('oz.client');
        }
    }
}
