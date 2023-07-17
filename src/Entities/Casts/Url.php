<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use InvalidArgumentException;
use OWC\Zaaksysteem\Entities\Entity;

class Url extends AbstractCast
{
    public function set(Entity $model, string $key, $value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("Invalid URL given");
        }

        return $value;
    }

    public function get(Entity $model, string $key, $value)
    {
        return $value;
    }
}
