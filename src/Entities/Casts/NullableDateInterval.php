<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use DateInterval;
use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Entity;
use Throwable;

class NullableDateInterval extends AbstractCast
{
    public function set(Entity $model, string $key, $value): ?DateInterval
    {
        if (is_null($value) || (is_object($value) && $value instanceof Dateinterval)) {
            return $value;
        }

        try {
            return new DateInterval($value);
        } catch (Throwable $e) {
            throw new InvalidArgumentException("Invalid date interval given");
        }
    }

    public function get(Entity $model, string $key, $value): ?DateInterval
    {
        if (is_null($value) || (is_object($value) && $value instanceof Dateinterval)) {
            return $value;
        }

        return new DateInterval($value);
    }

    /**
     * @see https://news-web.php.net/php.internals/113336
     */
    public function serialize(string $name, $value)
    {
        return rtrim(str_replace(
            ['M0S', 'H0M', 'DT0H', 'M0D', 'P0Y', 'Y0M', 'P0M'],
            ['M', 'H', 'DT', 'M', 'P', 'Y', 'P'],
            $value->format('P%yY%mM%dDT%hH%iM%sS')
        ), 'PT');
    }
}
