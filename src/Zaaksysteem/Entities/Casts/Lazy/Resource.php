<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Entities\Casts\CastsAttributes;

abstract class Resource implements CastsAttributes
{
    public function set(Entity $model, string $key, $value)
    {
        if (is_null($value) || is_string($value) || (is_object($value) && $value instanceof Entity)) {
            return $value;
        }

        throw new InvalidArgumentException(sprintf(
            'Invalid "%s" given, expected <string> or <%s>, got <%s>',
            $key,
            Entity::class,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    public function get(Entity $model, string $key, $value): ?Entity
    {
        if (! is_string($value)) {
            return $value; // Nullable or Entity
        }

        $uuid = $this->isUrl($value) ? $this->getUuidFromUrl($value) : $value;

        $entity = $this->resolveResource($uuid);

        // Assign the resolved zaaktype back to the model, so we won't
        // load it again whenever this attribute is accessed again.
        $model->setAttributeValue($key, $entity);

        return $entity;
    }

    public function serialize(string $name, $value)
    {
        if (is_object($value) && $value instanceof Entity) {
            return $value->url;
        }

        return $value;
    }

    abstract protected function resolveResource(string $uuid): ?Entity;

    protected function isUrl(string $value): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_URL);
    }

    protected function getUuidFromUrl(string $url): string
    {
        return substr($url, strrpos($url, '/') + 1);
    }
}
