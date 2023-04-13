<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Client;

use OWC\Zaaksysteem\Endpoint\ZakenEndpoint;
use OWC\Zaaksysteem\Endpoint\StatussenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaaktypenEndpoint;
use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Endpoint\StatustypenEndpoint;
use OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator;

class DecosJoinClient extends Client
{
    public const AVAILABLE_ENDPOINTS = [
        'zaken'         => ZakenEndpoint::class,
        'zaaktypen'     => ZaaktypenEndpoint::class,
        'statussen'     => StatussenEndpoint::class,
        'statustypen'   => StatustypenEndpoint::class,

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
