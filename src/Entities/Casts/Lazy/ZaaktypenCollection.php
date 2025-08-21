<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Zaaktype as ZaaktypeEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class ZaaktypenCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?ZaaktypeEntity
    {
        return resolve($this->clientName)->zaaktypen()->get($uuid);
    }
}
