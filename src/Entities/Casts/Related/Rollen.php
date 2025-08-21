<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Related;

use OWC\Zaaksysteem\Endpoints\Filter\RollenFilter;
use OWC\Zaaksysteem\Entities\Entity;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

use OWC\Zaaksysteem\Support\Collection;

class Rollen extends ResourceCollection
{
    public function resolveRelatedResourceCollection(Entity $entity): Collection
    {
        $rollenEndpoint = resolve($this->clientName)->rollen();

        $filter = new RollenFilter();

        return $rollenEndpoint->filter($filter->byZaak($entity));
    }
}
