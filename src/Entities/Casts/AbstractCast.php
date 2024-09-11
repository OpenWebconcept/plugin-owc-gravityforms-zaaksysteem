<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use OWC\Zaaksysteem\Entities\Entity;

abstract class AbstractCast implements CastsAttributes
{
    protected string $clientName;
    protected string $clientNamePretty;

    public function __construct(string $clientName, string $clientNamePretty)
    {
        $this->clientName = $clientName;
        $this->clientNamePretty = $clientNamePretty;
    }

    public function get(Entity $model, string $key, $value)
    {
        return $value;
    }

    public function set(Entity $model, string $key, $value)
    {
        return $value;
    }

    public function serialize(string $name, $value)
    {
        return $value;
    }
}
