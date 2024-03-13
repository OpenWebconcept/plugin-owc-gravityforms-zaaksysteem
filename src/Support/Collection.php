<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Support;

use Closure;

class Collection extends Enumerable implements CollectionInterface
{
    use Sortable;

    public const SORT_REVERSE = true;

    public static function collect(iterable $data): CollectionInterface
    {
        return new self($data);
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }

    public function all(): iterable
    {
        return $this->data;
    }

    public function take($limit): iterable
    {
        return array_slice($this->data, 0, $limit);
    }

    public function push($item): CollectionInterface
    {
        $this->data[] = $item;

        return $this;
    }

    public function count()
    {
        return count($this->data);
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function isNotEmpty(): bool
    {
        return $this->isEmpty() === false;
    }

    public function nth(int $index, $default)
    {
        return $this->get($index, $default);
    }

    public function first()
    {
        return reset($this->data);
    }

    public function last()
    {
        return end($this->data);
    }

    public function keys(): CollectionInterface
    {
        return static::collect(array_keys($this->data));
    }

    public function filter(Closure $predicate): CollectionInterface
    {
        return static::collect(array_filter($this->data, $predicate));
    }

    public function map(Closure $callback): CollectionInterface
    {
        return static::collect(array_map($callback, $this->data));
    }

    public function flatten(Closure $callback, $initial = null)
    {
        return array_reduce($this->data, $callback, $initial);
    }

    public function flattenAndAssign(Closure $callback, $initial = null)
    {
        $this->data = array_reduce($this->data, $callback, $initial);

        return $this;
    }

    public function groupBy(Closure $callback): CollectionInterface
    {
        $results = [];

        foreach ($this->data as $key => $value) {
            $groupKeys = $callback($value, $key);

            if (! is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                $groupKey = is_bool($groupKey) ? (int) $groupKey : $groupKey;

                if (! array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new static([]);
                }

                $results[$groupKey]->offsetSet($key, $value);
            }
        }

        return static::collect($results);
    }
}
