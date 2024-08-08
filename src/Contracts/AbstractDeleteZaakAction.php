<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use OWC\Zaaksysteem\Resolvers\ContainerResolver;

abstract class AbstractDeleteZaakAction
{
    public const CLIENT_NAME = '';

    protected string $supplier;

    abstract public function deleteZaak(string $zaakURL, array $entry, array $form): void;

    protected function getApiClient(): Client
    {
        return ContainerResolver::make()->getApiClient(static::CLIENT_NAME);
    }
}
