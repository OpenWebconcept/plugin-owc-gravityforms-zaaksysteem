<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Eigenschap extends Entity
{
    protected array $casts = [
        'zaaktype' => Casts\Lazy\Zaaktype::class,
        'status' => Casts\Lazy\Status::class,
    ];
}
