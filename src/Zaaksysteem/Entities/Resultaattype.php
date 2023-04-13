<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Resultaattype extends Entity
{
    protected array $casts = [
    // 'url' => "http://example.com",
    'zaaktype' => Casts\Lazy\Zaaktype::class,
    // 'omschrijving' => "string",
    // 'resultaattypeomschrijving' => "http://example.com",
    // 'omschrijvingGeneriek' => "string",
    // 'selectielijstklasse' => "http://example.com",
    // 'toelichting' => "string",
    // 'archiefnominatie' => "blijvend_bewaren",
    // 'archiefactietermijn' => "string",
    // 'brondatumArchiefprocedure' =>
    ];
}
