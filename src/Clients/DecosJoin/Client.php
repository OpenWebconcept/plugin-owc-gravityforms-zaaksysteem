<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin;

use OWC\Zaaksysteem\Contracts\AbstractClient;
use OWC\Zaaksysteem\Endpoints\EigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoints\EnkelvoudiginformatieobjectenEndpoint;
use OWC\Zaaksysteem\Endpoints\InformatieobjecttypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ObjectinformatieEndpoint;
use OWC\Zaaksysteem\Endpoints\RollenEndpoint;
use OWC\Zaaksysteem\Endpoints\RoltypenEndpoint;
use OWC\Zaaksysteem\Endpoints\StatussenEndpoint;
use OWC\Zaaksysteem\Endpoints\StatustypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaakeigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaakinformatieobjectenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaaktypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZakenEndpoint;

class Client extends AbstractClient
{
    public const CLIENT_NAME = 'decos-join';
    public const CALLABLE_NAME = 'dj.client';

    public const AVAILABLE_ENDPOINTS = [
        // Zaken API.
        'zaken' => [ZakenEndpoint::class, 'zaken'],
        'statussen' => [StatussenEndpoint::class, 'zaken'],
        'rollen' => [RollenEndpoint::class, 'zaken'],
        'zaakeigenschappen' => [ZaakeigenschappenEndpoint::class, 'zaken'],
        'zaakinformatieobjecten' => [ZaakinformatieobjectenEndpoint::class, 'zaken'],

        // Catalogi API.
        'zaaktypen' => [ZaaktypenEndpoint::class, 'catalogi'],
        'statustypen' => [StatustypenEndpoint::class, 'catalogi'],
        'roltypen' => [RoltypenEndpoint::class, 'catalogi'],
        'informatieobjecttypen' => [InformatieobjecttypenEndpoint::class, 'catalogi'],
        'eigenschappen' => [EigenschappenEndpoint::class, 'catalogi'],

        // Documenten API
        'objectinformatieobjecten' => [ObjectinformatieEndpoint::class, 'documenten'],
        'enkelvoudiginformatieobjecten' => [EnkelvoudiginformatieobjectenEndpoint::class, 'documenten'],

        /**
         * Not yet implemented
         */
        // 'zgw.klantcontacten' => Endpoint::class,
        // 'zgw.resultaten' => Endpoint::class,
        // 'zgw.rollen' => Endpoint::class,
        // 'zgw.zaakcontactmomenten' => Endpoint::class,
        // 'zgw.zaakinformatieobjecten' => Endpoint::class,
        // 'zgw.zaakobjecten' => Endpoint::class,
        // 'zgw.zaakverzoeken' => Endpoint::class,
    ];
}
