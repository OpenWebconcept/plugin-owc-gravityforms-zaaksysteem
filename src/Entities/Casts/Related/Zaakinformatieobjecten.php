<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Related;

use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Endpoints\Filter\ZaakinformatieobjectenFilter;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

class Zaakinformatieobjecten extends ResourceCollection
{
    public function resolveRelatedResourceCollection(Entity $entity): Collection
    {
        $statussenEndpoint = resolve($this->clientName)->zaakinformatieobjecten();

        $filter = new ZaakinformatieobjectenFilter();

        return $statussenEndpoint->filter($filter->byZaak($entity));
    }
}
