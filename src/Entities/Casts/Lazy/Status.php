<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Status as StatusEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Status extends Resource
{
    protected function resolveResource(string $uuid): ?StatusEntity
    {
        return resolve($this->clientName)->statussen()->get($uuid);
    }
}
