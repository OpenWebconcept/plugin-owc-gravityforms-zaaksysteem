<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Related;

use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Endpoint\Filter\StatussenFilter;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Statussen extends ResourceCollection
{
    public function resolveRelatedResourceCollection(Entity $entity): Collection
    {
        $statussenEndpoint = resolve($this->clientName)->statussen();

        $filter = new StatussenFilter();

        return $statussenEndpoint->filter($filter->byZaak($entity));
    }
}
