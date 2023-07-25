<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Casts;

use OWC\Zaaksysteem\Entities\Entity;

interface CastsAttributes
{
    public function __construct(string $clientName);
    public function get(Entity $model, string $key, $value);
    public function set(Entity $model, string $key, $value);
    public function serialize(string $name, $value);
}
