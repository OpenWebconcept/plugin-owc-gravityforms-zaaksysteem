<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\OpenZaak;

use OWC\Zaaksysteem\Endpoints\ {
    ZakenEndpoint, RollenEndpoint, RoltypenEndpoint, StatussenEndpoint,
    ZaaktypenEndpoint, ResultatenEndpoint, StatustypenEndpoint,
    CatalogussenEndpoint, ZaakobjectenEndpoint, ResultaattypenEndpoint,
    ObjectinformatieEndpoint, ZaakinformatieobjectenEndpoint,
    InformatieobjecttypenEndpoint, EnkelvoudiginformatieobjectenEndpoint,
    ZaakeigenschappenEndpoint, EigenschappenEndpoint
};
use OWC\Zaaksysteem\Contracts\AbstractClient;

class Client extends AbstractClient
{
    public const CLIENT_NAME = 'openzaak';
    public const CALLABLE_NAME = 'oz.client';

    public const AVAILABLE_ENDPOINTS = [
        // Zaken API
        'zaken'                     => ZakenEndpoint::class,
        'statussen'                 => StatussenEndpoint::class,
        'rollen'                    => RollenEndpoint::class,
        'resultaten'                => ResultatenEndpoint::class,
        'zaakeigenschappen'         => ZaakeigenschappenEndpoint::class,
        'zaakinformatieobjecten'    => ZaakinformatieobjectenEndpoint::class,
        'zaakobjecten'              => ZaakobjectenEndpoint::class,

        /**
         * Not yet implemented
         */
        // 'zaakcontactmomenten' => Endpoint::class,
        // 'zaakverzoeken' => Endpoint::class,

        // Catalogi API
        'zaaktypen'         => ZaaktypenEndpoint::class,
        'statustypen'       => StatustypenEndpoint::class,
        'roltypen'          => RoltypenEndpoint::class,
        'catalogussen'      => CatalogussenEndpoint::class,
        'resultaattypen'    => ResultaattypenEndpoint::class,
        'informatieobjecttypen' => InformatieobjecttypenEndpoint::class,
        'eigenschappen' => EigenschappenEndpoint::class,

        /**
         * Not yet implemented
         */
        // 'besluittypen' => Endpoint::class,
        // 'zaaktype-informatieobjecttypen' => Endpoint::class,

        // Documenten API
        'objectinformatieobjecten' => ObjectinformatieEndpoint::class,
        'enkelvoudiginformatieobjecten' => EnkelvoudiginformatieobjectenEndpoint::class,
        /**
         * Not yet implemented
         */
        // 'gebruiksrechten' => Endpoint::class,
        // 'objectinformatieobjecten' => Endpoint::class,
        // 'bestandsdelen' => Endpoint::class,
    ];
}
