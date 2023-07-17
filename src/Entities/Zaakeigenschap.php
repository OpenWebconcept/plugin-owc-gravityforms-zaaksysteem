<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Zaakeigenschap extends Entity
{
    protected array $casts = [
        // 'url' => "http://example.com",
        // 'uuid' => "095be615-a8ad-4c33-8e9c-c7612fbf6c9f",
        'zaak' => Casts\Lazy\Zaak::class,
        // 'eigenschap' => "http://example.com",
        // 'naam' => "string",
        // 'waarde' => "string",
    ];
}
