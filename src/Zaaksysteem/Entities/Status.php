<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Status extends Entity
{
    protected array $casts = [
        // 'url' => "http://example.com",
        // 'uuid' => "095be615-a8ad-4c33-8e9c-c7612fbf6c9f",
        'zaak' => Casts\Lazy\Zaak::class,
        'statustype' => Casts\Lazy\Statustype::class,
        'datumStatusGezet' => Casts\NullableDateTime::class,
        // 'statustoelichting' => "string"
    ];
}
