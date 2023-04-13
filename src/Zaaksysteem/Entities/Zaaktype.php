<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Zaaktype extends Entity
{
    protected array $casts = [
        // 'url' => SomeClass::class,
        // 'identificatie' => SomeClass::class,
        // 'omschrijving' => SomeClass::class,
        // 'omschrijvingGeneriek' => SomeClass::class,
        'vertrouwelijkheidaanduiding' => Casts\Confidentiality::class,
        // 'doel' => SomeClass::class,
        // 'aanleiding' => SomeClass::class,
        // 'toelichting' => SomeClass::class,
        // 'indicatieInternOfExtern' => SomeClass::class,
        // 'handelingInitiator' => SomeClass::class,
        // 'onderwerp' => SomeClass::class,
        // 'handelingBehandelaar' => SomeClass::class,
        'doorlooptijd' => Casts\NullableDateInterval::class,
        // 'servicenorm' => SomeClass::class,
        // 'opschortingEnAanhoudingMogelijk' => SomeClass::class,
        // 'verlengingMogelijk' => SomeClass::class,
        'verlengingstermijn' => Casts\NullableDateInterval::class,
        // 'trefwoorden' => SomeClass::class,
        // 'publicatieIndicatie' => SomeClass::class,
        // 'publicatietekst' => SomeClass::class,
        // 'verantwoordingsrelatie' => SomeClass::class,
        // 'productenOfDiensten' => SomeClass::class,
        // 'selectielijstProcestype' => SomeClass::class,
        // 'referentieproces' => SomeClass::class,
        'catalogus' => Casts\Lazy\Catalogus::class,
        'statustypen' => Casts\Lazy\StatustypenCollection::class,
        // 'resultaattypen' => SomeClass::class,
        // 'eigenschappen' => SomeClass::class,
        // 'informatieobjecttypen' => SomeClass::class,
        'roltypen' => Casts\Lazy\RoltypenCollection::class,
        // 'besluittypen' => SomeClass::class,
        'deelzaaktypen' => Casts\Lazy\ZaaktypenCollection::class,
        'gerelateerdeZaaktypen' => Casts\Lazy\ZaaktypenCollection::class,
        'beginGeldigheid' => Casts\NullableDate::class,
        'eindeGeldigheid' => Casts\NullableDate::class,
        'versiedatum' => Casts\NullableDate::class,
        // 'concept' => SomeClass::class,
    ];
}
