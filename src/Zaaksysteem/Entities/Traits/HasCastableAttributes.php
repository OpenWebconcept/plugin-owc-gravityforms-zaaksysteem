<?php

namespace OWC\Zaaksysteem\Entities\Traits;

use OWC\Zaaksysteem\Entities\Casts\CastsAttributes;

trait HasCastableAttributes
{
    public function getAttributeValue(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function setAttributeValue(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function hasAttributeValue(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function getAttributeNames(): array
    {
        return array_keys($this->data);
    }

    protected function hasCast(string $name): bool
    {
        return isset($this->casts[$name]);
    }

    protected function getCaster(string $name): CastsAttributes
    {
        $caster = $this->casts[$name];

        if (strpos($caster, ':') === false) {
            return new $caster($this->clientName);
        }

        [$class, $constructorArg] = explode(':', $caster);

        return new $class($this->clientName, $constructorArg);
    }
}
