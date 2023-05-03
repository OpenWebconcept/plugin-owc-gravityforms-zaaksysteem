<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Related;

use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Endpoint\Filter\RollenFilter;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Rollen extends ResourceCollection
{
    public function resolveRelatedResourceCollection(Entity $entity): Collection
    {
        $rollenEndpoint = resolve($this->clientName)->rollen();

        $filter = new RollenFilter();

        return $rollenEndpoint->filter($filter->byZaak($entity));
    }
}
