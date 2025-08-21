<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Related;

use OWC\Zaaksysteem\Entities\Casts\AbstractCast;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Support\Collection;

abstract class ResourceCollection extends AbstractCast
{
    public function set(Entity $model, string $key, $value)
    {
        return null; // Don't allow setting related models.
    }

    public function get(Entity $model, string $key, $value): ?Collection
    {
        if (! empty($value)) {
            return $value;
        }

        $collection = $this->resolveRelatedResourceCollection($model);

        // Assign the resolved collection back to the model, so we won't
        // load it again whenever this attribute is accessed again.
        $model->setAttributeValue($key, $collection);

        return $collection;
    }

    public function serialize(string $name, $value)
    {
        return null;
    }

    abstract protected function resolveRelatedResourceCollection(Entity $entity): Collection;
}
