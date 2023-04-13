<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Entities\Attributes\SubjectType as SubjectTypeAttribute;

class SubjectType extends AbstractCast
{
    public function set(Entity $model, string $key, $value): ?string
    {
        if (! SubjectTypeAttribute::isValidValue($value)) {
            throw new InvalidArgumentException("Invalid subject type for {$key} given");
        }

        return $value;
    }

    public function get(Entity $model, string $key, $value): ?SubjectTypeAttribute
    {
        return new SubjectTypeAttribute($value);
    }

    public function serialize(string $name, $value)
    {
        return $value->get();
    }
}
