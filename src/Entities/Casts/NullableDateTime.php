<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Entity;

class NullableDateTime extends AbstractCast
{
    protected string $format;

    public function __construct(string $clientName, string $clientNamePretty, string $format = 'Y-m-d\\TH:i:sp')
    {
        parent::__construct($clientName, $clientNamePretty);
        $this->format = $format;
    }

    public function set(Entity $model, string $key, $value): ?string
    {
        if (is_null($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = new DateTimeImmutable($value);
        }

        if (! is_object($value)) {
            throw new InvalidArgumentException("Invalid date given");
        }

        return $value->format($this->format);
    }

    public function get(Entity $model, string $key, $value): ?DateTimeImmutable
    {
        return is_string($value) ? new DateTimeImmutable($value) : null;
    }

    public function serialize(string $name, $value)
    {
        return is_object($value) && $value instanceof DateTimeInterface ?
            $value->format($this->format) :
            $value;
    }
}
