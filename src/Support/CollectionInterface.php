<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Support;

use Closure;

interface CollectionInterface
{
    public static function collect(iterable $data): CollectionInterface;
    public function get($key, $default = null);
    public function has($key);
    public function all(): iterable;
    public function count();
    public function isEmpty(): bool;
    public function isNotEmpty(): bool;
    public function nth(int $index, $default);
    public function first();
    public function last();
    public function filter(Closure $predicate): CollectionInterface;
    public function map(Closure $callback): CollectionInterface;
    public function toArray(): iterable;
}
