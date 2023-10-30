<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Zaak extends Entity
{
    protected array $casts = [
        'url' => Casts\Url::class,
        // 'uuid'  => SomeClass::class,
        // 'identificatie' => SomeClass::class,
        // 'bronorganisatie'   => SomeClass::class,
        // 'omschrijving'  => SomeClass::class,
        // 'toelichting'   => SomeClass::class,
        'zaaktype' => Casts\Lazy\Zaaktype::class,
        'registratiedatum' => Casts\NullableDate::class,
        // 'verantwoordelijkeOrganisatie'  => SomeClass::class,
        'startdatum'    => Casts\NullableDate::class,
        'einddatum' => Casts\NullableDate::class,
        'einddatumGepland' => Casts\NullableDate::class,
        'uiterlijkeEinddatumAfdoening' => Casts\NullableDate::class,
        'publicatiedatum' => Casts\NullableDate::class,
        // 'communicatiekanaal'    => SomeClass::class,
        // 'productenOfDiensten'   => SomeClass::class,
        'vertrouwelijkheidaanduiding' => Casts\Confidentiality::class,
        // 'betalingsindicatie'    => SomeClass::class,
        // 'betalingsindicatieWeergave'    => SomeClass::class,
        'laatsteBetaaldatum' => Casts\NullableDateTime::class,
        // 'zaakgeometrie' => SomeClass::class,
        // 'verlenging'    => SomeClass::class,
        // 'opschorting'   => SomeClass::class,
        // 'selectielijstklasse'   => SomeClass::class,
        'hoofdzaak' => Casts\Lazy\Zaak::class,
        'deelzaken' => Casts\Lazy\ZaakCollection::class,
        // 'relevanteAndereZaken'  => SomeClass::class,
        // 'eigenschappen' => SomeClass::class,
        'status' => Casts\Lazy\Status::class,
        // 'kenmerken' => SomeClass::class,
        // 'archiefnominatie'  => SomeClass::class,
        // 'archiefstatus' => SomeClass::class,
        'archiefactiedatum' => Casts\NullableDate::class,
        'resultaat' => Casts\Lazy\Resultaat::class,
        // 'opdrachtgevendeOrganisatie'    => SomeClass::class,

        'statussen' => Casts\Related\Statussen::class,
        'zaakinformatieobjecten' => Casts\Related\Zaakinformatieobjecten::class,
        'rollen' => Casts\Related\Rollen::class,
    ];

    public function title(): string
    {
        return $this->omschrijving;
    }

    /**
     * Is used in overview generated by owc/mijn-zaken block.
     */
    public function getPermalink(): string
    {
        $supplier = $this->getSupplier();

        if (empty($supplier)) {
            return sprintf('%s/zaak/%s', get_site_url(), $this->identificatie);
        }

        return sprintf('%s/zaak/%s/%s', get_site_url(), $this->identificatie, $supplier);
    }

    protected function getSupplier(): string
    {
        return $this->leverancier ?? '';
    }

    /**
     * Wether or not the current Zaak is initiated by the given BSN.
     */
    public function isInitiatedBy(string $bsn): bool
    {
        $validRollen = $this->rollen->filter(function (Rol $rol) use ($bsn) {
            return $rol->isInitiator()
                && $rol->betrokkeneType->is('natuurlijk_persoon')
                && $rol->betrokkeneIdentificatie['inpBsn'] === $bsn;
        });

        return $validRollen->isNotEmpty();
    }
}
