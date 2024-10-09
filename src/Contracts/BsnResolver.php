<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

interface BsnResolver
{
    public static function make(): self;
    public function bsn(): string;
}
