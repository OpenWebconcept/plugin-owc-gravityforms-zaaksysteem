<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Statustype as StatustypeEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Statustype extends Resource
{
    protected function resolveResource(string $uuid): ?StatustypeEntity
    {
        return resolve('api.client')->statustypen()->get($uuid);
    }
}
