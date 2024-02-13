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

    public function statusExplanation(): string
    {
        return $this->getValue('omschrijving', '');
    }

    public function volgnummer(): string
    {
        $volgnummer = (string) $this->getValue('volgnummer', '');

        return ltrim($volgnummer, '0');
    }

    public function processStatus(): string
    {
        return (string) $this->getValue('processStatus', '');
    }

    public function isCurrent(): bool
    {
        $status = $this->processStatus();

        return 'current' === $status;
    }

    public function isPast(): bool
    {
        $status = $this->processStatus();

        return 'past' === $status;
    }

    public function isFuture(): bool
    {
        $status = $this->processStatus();

        return 'future' === $status;
    }
}
