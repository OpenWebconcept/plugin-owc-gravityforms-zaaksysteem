<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Attributes\Confidentiality as ConfidentialityAttribute;
use OWC\Zaaksysteem\Entities\Attributes\EnumAttribute;
use OWC\Zaaksysteem\Entities\Entity;

class Confidentiality extends AbstractCast
{
    public function set(Entity $model, string $key, $value): ?string
    {
        if (! ConfidentialityAttribute::isValidValue($value)) {
            throw new InvalidArgumentException("Invalid confidentiality level for {$key} given");
        }

        return $value;
    }

    public function get(Entity $model, string $key, $value): ?ConfidentialityAttribute
    {
        return is_string($value) ? new ConfidentialityAttribute($value) : null;
    }

    public function serialize(string $name, $value): string
    {
        return (is_object($value) && $value instanceof EnumAttribute) ? $value->get() : $value;
    }
}
