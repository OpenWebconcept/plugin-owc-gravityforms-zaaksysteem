<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Statustype;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class StatustypenCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?Statustype
    {
        return resolve($this->clientName)->statustypen()->get($uuid);
    }
}
