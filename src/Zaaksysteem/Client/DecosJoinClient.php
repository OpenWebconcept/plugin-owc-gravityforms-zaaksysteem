<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Client;

use OWC\Zaaksysteem\Endpoint\EigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoint\RoltypenEndpoint;
use OWC\Zaaksysteem\Endpoint\StatussenEndpoint;
use OWC\Zaaksysteem\Endpoint\StatustypenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaakeigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaaktypenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZakenEndpoint;
use OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator;
use OWC\Zaaksysteem\Http\RequestClientInterface;

class DecosJoinClient extends Client
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
        'roltypen' => RoltypenEndpoint::class,
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

    protected array $container = [];
    protected RequestClientInterface $client;
    protected TokenAuthenticator $authenticator;

    // Does every API require token authentication? Maybe replace with interface
    public function __construct(RequestClientInterface $client, TokenAuthenticator $authenticator)
    {
        $this->client = $client;
        $this->authenticator = $authenticator;
    }
}
