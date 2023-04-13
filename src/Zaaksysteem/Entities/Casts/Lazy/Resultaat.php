<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Resultaat as ResultaatEntity;

use function OWC\Zaaksysteem\tap;

class Resultaat extends Resource
{
    protected function resolveResource(string $uuid): ?ResultaatEntity
    {
        return tap('api.client')->resultaten()->get($uuid);
    }
}
