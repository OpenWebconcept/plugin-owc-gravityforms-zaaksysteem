<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Roltype as RoltypeEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Roltype extends Resource
{
    protected string $resourceType = RoltypeEntity::class;

    protected function resolveResource(string $uuid): ?RoltypeEntity
    {
        return resolve($this->clientName)->roltypen()->get($uuid);
    }
}
