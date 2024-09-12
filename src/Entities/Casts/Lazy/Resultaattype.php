<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Resultaattype as ResultaattypeEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Resultaattype extends Resource
{
    protected string $resourceType = ResultaattypeEntity::class;

    protected function resolveResource(string $uuid): ?ResultaattypeEntity
    {
        return resolve($this->clientName)->resultaattypen()->get($uuid);
    }
}
