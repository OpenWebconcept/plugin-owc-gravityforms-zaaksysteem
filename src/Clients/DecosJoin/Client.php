<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin;

use OWC\Zaaksysteem\Contracts\AbstractClient;
use OWC\Zaaksysteem\Endpoint\EigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaakeigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoints\StatussenEndpoint;
use OWC\Zaaksysteem\Endpoints\StatustypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaaktypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZakenEndpoint;

class Client extends AbstractClient
{
    public const CLIENT_NAME = 'decosjoin';
    public const CALLABLE_NAME = 'dj.client';

    public const AVAILABLE_ENDPOINTS = [
        'zaken' => ZakenEndpoint::class,
        'statussen' => StatussenEndpoint::class,
        'zaakeigenschappen' => ZaakeigenschappenEndpoint::class,

        // Catalogi API
        'zaaktypen' => ZaaktypenEndpoint::class,
        'statustypen' => StatustypenEndpoint::class,
        'eigenschappen' => EigenschappenEndpoint::class,

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
