<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Client;

use OWC\Zaaksysteem\Endpoint\CatalogussenEndpoint;
use OWC\Zaaksysteem\Endpoint\EigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoint\EnkelvoudiginformatieobjectenEndpoint;
use OWC\Zaaksysteem\Endpoint\InformatieobjecttypenEndpoint;
use OWC\Zaaksysteem\Endpoint\ObjectinformatieEndpoint;
use OWC\Zaaksysteem\Endpoint\ResultaattypenEndpoint;
use OWC\Zaaksysteem\Endpoint\ResultatenEndpoint;
use OWC\Zaaksysteem\Endpoint\RollenEndpoint;
use OWC\Zaaksysteem\Endpoint\RoltypenEndpoint;
use OWC\Zaaksysteem\Endpoint\StatussenEndpoint;
use OWC\Zaaksysteem\Endpoint\StatustypenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaakeigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaakinformatieobjectenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaakobjectenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZaaktypenEndpoint;
use OWC\Zaaksysteem\Endpoint\ZakenEndpoint;
use OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator;
use OWC\Zaaksysteem\Http\RequestClientInterface;

class OpenZaakClient extends Client
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
