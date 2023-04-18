<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts\Lazy;

use RuntimeException;
use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Entities\Casts\AbstractCast;

abstract class ResourceCollection extends AbstractCast
{
    public function set(Entity $model, string $key, $value)
    {
        if (! is_iterable($value)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid "%s" given, expected Iterable value',
                $key
            ));
        }

        // This check will be redundant from PHP 8.2 onward as array_fillter
        // will then accept both arrays and Iterable objects.
        if (! is_array($value)) {
            $value = iterator_to_array($value);
        }

        return array_filter($value, function ($item) {
            return is_null($item)
                || is_string($item)
                || (is_object($item) && $item instanceof Entity);
        });
    }

    public function get(Entity $model, string $key, $value): ?Collection
    {
        if (! is_iterable($value)) {
            throw new RuntimeException(sprintf(
                'Unable to cast "%s" to %s; value is not iterable',
                $key,
                static::class
            ));
        }

        $collection = Collection::collect($value)->map(function ($item) {
            if (is_object($item) && $item instanceof Entity) {
                return $item;
            }

            $uuid = $this->isUrl($item) ? $this->getUuidFromUrl($item) : $item;

            return $this->resolveResource($uuid);
        });

        // Assign the resolved zaaktype back to the model, so we won't
        // load it again whenever this attribute is accessed again.
        $model->setAttributeValue($key, $collection);

        return $collection;
    }

    public function serialize(string $name, $value)
    {
        return array_map(function ($item) {
            if (is_object($item) && $item instanceof Entity) {
                return $item->url;
            }

            return $item;
        }, $value);
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
