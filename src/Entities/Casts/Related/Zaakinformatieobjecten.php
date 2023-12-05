<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Related;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use OWC\Zaaksysteem\Endpoints\Filter\ZaakinformatieobjectenFilter;
use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;

use OWC\Zaaksysteem\Support\Collection;

class Zaakinformatieobjecten extends ResourceCollection
{
    public function resolveRelatedResourceCollection(Entity $entity): Collection
    {
        $statussenEndpoint = resolve($this->clientName)->zaakinformatieobjecten();
        $filter = new ZaakinformatieobjectenFilter();

        $objects = $statussenEndpoint->filter($filter->byZaak($entity));

        return $objects->filter(function ($object) {
            if (! $object instanceof Zaakinformatieobject || ! $object->informatieobject instanceof Enkelvoudiginformatieobject) {
                return false;
            }

            return ! $object->informatieobject->isClassified() && $object->informatieobject->hasFinalStatus();
        });
    }
}
