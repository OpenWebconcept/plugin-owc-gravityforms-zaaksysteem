<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

interface IdentificationResolver
{
    public static function make(): self;
    public function get(): string;
}
