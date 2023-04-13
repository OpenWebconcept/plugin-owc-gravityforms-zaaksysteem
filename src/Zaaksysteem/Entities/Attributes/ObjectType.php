<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Attributes;

class ObjectType extends EnumAttribute
{
    public const VALID_MEMBERS = [
        'adres', 'besluit', 'buurt', 'enkelvoudig_document', 'gemeente',
        'gemeentelijke_openbare_ruimte', 'huishouden', 'inrichtingselement',
        'kadastrale_onroerende_zaak', 'kunstwerkdeel', 'maatschappelijke_activiteit',
        'medewerker', 'natuurlijk_persoon', 'niet_natuurlijk_persoon',
        'openbare_ruimte', 'organisatorische_eenheid', 'pand', 'spoorbaandeel',
        'status', 'terreindeel', 'terrein_gebouwd_object', 'vestiging',
        'waterdeel', 'wegdeel', 'wijk', 'woonplaats', 'woz_deelobject',
        'woz_object', 'woz_waarde', 'zakelijk_recht', 'overige',
    ];

    protected string $name = 'object type';
}
