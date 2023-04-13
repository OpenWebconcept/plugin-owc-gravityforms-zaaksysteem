<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Zaak as ZaakEntity;

use function OWC\Zaaksysteem\tap;

class Zaak extends Resource
{
    protected function resolveResource(string $uuid): ?ZaakEntity
    {
        return tap('api.client')->zaken()->get($uuid);
    }
}
