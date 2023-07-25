<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Roltype;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class RoltypenCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?Roltype
    {
        return resolve($this->clientName)->roltypen()->get($uuid);
    }
}
