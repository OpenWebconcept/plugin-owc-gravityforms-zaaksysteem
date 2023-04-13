<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

use ArrayAccess;
use JsonSerializable;

abstract class Entity implements
    ArrayAccess,
    JsonSerializable,
    Contracts\Jsonable,
    Contracts\Arrayable
{
    use Traits\Arrayable;
    use Traits\HasCastableAttributes;

    protected array $data;
    protected array $casts = [];

    public function __construct(array $itemData = [])
    {
        $this->hydrate($itemData);
    }

    public function __get($name)
    {
        return $this->getValue($name);
    }

    public function __set($name, $value)
    {
        return $this->setValue($name, $value);
    }

    public function getValue(string $name, $default = null)
    {
        $value = $this->getAttributeValue($name, $default);

        if ($this->hasCast($name)) {
            $caster = $this->getCaster($name);

            return $caster->get($this, $name, $value);
        }

        return $value;
    }

    public function setValue(string $name, $value)
    {
        if ($this->hasCast($name)) {
            $caster = $this->getCaster($name);

            $value = $caster->set($this, $name, $value);
        }

        return $this->setAttributeValue($name, $value);
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->getAttributeNames() as $name) {
            $data[$name] = $this->serializeAttribute($name);
        }

        return $data;
    }

    public function attributesToArray(): array
    {
        return $this->data;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson(int $flags = 0, int $depth = 512): string
    {
        return json_encode($this, $flags, $depth);
    }

    protected function serializeAttribute(string $name)
    {
        if (! $this->hasCast($name)) {
            return $this->getValue($name);
        }

        return $this->getCaster($name)->serialize($name, $this->getAttributeValue($name));
    }

    protected function hydrate(array $data)
    {
        foreach ($data as $name => $value) {
            $this->setValue($name, $value);
        }
    }
}
