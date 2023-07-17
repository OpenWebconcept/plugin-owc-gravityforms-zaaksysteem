<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Attributes;

use InvalidArgumentException;

abstract class EnumAttribute
{
    public const VALID_MEMBERS = [];

    protected string $name = 'Enum';
    protected string $value;

    public function __construct(string $value)
    {
        if (! static::isValidValue($value)) {
            throw new InvalidArgumentException("Unknown {$this->name} given");
        }

        $this->value = $value;
    }

    public static function isValidValue(string $value): bool
    {
        return in_array($value, static::VALID_MEMBERS);
    }

    public function __toString()
    {
        return $this->get();
    }

    public function get(): string
    {
        return $this->value;
    }

    public function is(string $value): bool
    {
        return $this->value === $value;
    }

    public function isnt(string $value): bool
    {
        return ! $this->is($value);
    }
}
