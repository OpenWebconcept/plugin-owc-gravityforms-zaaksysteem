<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Roltype as RoltypeEntity;

use function OWC\Zaaksysteem\tap;

class Roltype extends Resource
{
    protected function resolveResource(string $uuid): ?RoltypeEntity
    {
        return tap('api.client')->roltypen()->get($uuid);
    }
}
