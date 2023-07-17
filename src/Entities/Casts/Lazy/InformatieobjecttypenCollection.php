<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Informatieobjecttype;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class InformatieobjecttypenCollection extends ResourceCollection
{
    protected function resolveResource(string $uuid): ?Informatieobjecttype
    {
        return resolve($this->clientName)->informatieobjecttypen()->get($uuid);
    }
}
