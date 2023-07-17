<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Zaak;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class ZaakCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?Zaak
    {
        return resolve($this->clientName)->zaken()->get($uuid);
    }
}
