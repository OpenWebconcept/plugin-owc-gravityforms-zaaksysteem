<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Zaaktype as ZaaktypeEntity;

use function OWC\Zaaksysteem\tap;

class ZaaktypenCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?ZaaktypeEntity
    {
        return tap('api.client')->zaaktypen()->get($uuid);
    }
}
