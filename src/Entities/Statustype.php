<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Statustype extends Entity
{
    protected array $casts = [
        // 'url' => "http://example.com",
        // 'omschrijving' => "string",
        // 'omschrijvingGeneriek' => "string",
        // 'statustekst' => "string",
        'zaaktype' => Casts\Lazy\Zaaktype::class,
        // 'volgnummer' => 1,
        // 'isEindstatus' => true,
        // 'informeren' => true
    ];

    public function isEndStatus(): bool
    {
        return $this->getValue('isEindstatus', false);
    }

    public function statusExplanation(): string
    {
        return strtolower($this->getValue('omschrijving', ''));
    }
}
