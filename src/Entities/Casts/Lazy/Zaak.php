<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Zaak as ZaakEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Zaak extends Resource
{
    protected string $resourceType = ZaakEntity::class;

    protected function resolveResource(string $uuid): ?ZaakEntity
    {
        return resolve($this->clientName)->zaken()->get($uuid);
    }
}
