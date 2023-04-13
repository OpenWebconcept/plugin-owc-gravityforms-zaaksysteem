<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Resultaattype as ResultaattypeEntity;

use function OWC\Zaaksysteem\tap;

class Resultaattype extends Resource
{
    protected function resolveResource(string $uuid): ?ResultaattypeEntity
    {
        return tap('api.client')->resultaattypen()->get($uuid);
    }
}
