<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Roltype extends Entity
{
    protected array $casts = [
        // 'url' => SomeClass::class,
        'zaaktype' => Casts\Lazy\Zaaktype::class,
        // 'omschrijving' => SomeClass::class,
        // 'omschrijvingGeneriek' => SomeClass::class,
    ];
}
