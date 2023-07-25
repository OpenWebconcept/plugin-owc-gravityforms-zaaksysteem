<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

use OWC\Zaaksysteem\Entities\Zaaktype as ZaaktypeEntity;

class ZaaktypenCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?ZaaktypeEntity
    {
        return resolve($this->clientName)->zaaktypen()->get($uuid);
    }
}
