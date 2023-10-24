<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

interface Resolver
{
    public static function make(): self;
    public function get(string $key);
    public function bsn(): string;
}
