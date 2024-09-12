<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject as EnkelvoudiginformatieobjectEntity;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Enkelvoudiginformatieobject extends Resource
{
    protected string $resourceType = EnkelvoudiginformatieobjectEntity::class;

    protected function resolveResource(string $uuid): ?EnkelvoudiginformatieobjectEntity
    {
        return resolve($this->clientName)->enkelvoudiginformatieobjecten()->get($uuid);
    }
}
