<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Catalogus as CatalogusEntity;

use function OWC\Zaaksysteem\tap;

class Catalogus extends Resource
{
    protected function resolveResource(string $uuid): ?CatalogusEntity
    {
        return tap('api.client')->catalogussen()->get($uuid);
    }
}
