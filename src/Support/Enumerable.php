<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Support;

use ArrayAccess;
use Iterator;

abstract class Enumerable implements ArrayAccess, Iterator
{
    protected iterable $data;

    public function __construct(iterable $data)
    {
        $this->hydrate($data);
    }

    public function __get(string $key)
    {
        return $this->data[$key];
    }

    public function __set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset(string $key)
    {
        return isset($this->data[$key]);
    }

    public function __unset(string $key)
    {
        unset($this->data[$key]);
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function rewind(): void
    {
        reset($this->data);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    public function next(): void
    {
        next($this->data);
    }

    public function valid(): bool
    {
        return key($this->data) !== null;
    }

    public function toArray(): iterable
    {
        return $this->data;
    }

    protected function hydrate($data)
    {
        $this->data = is_array($data) ? $data : iterator_to_array($data);
    }
}
