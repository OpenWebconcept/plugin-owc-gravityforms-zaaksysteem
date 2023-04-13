<?php

namespace OWC\Zaaksysteem;

use DI\Container;

/**
 * Link interfaces to their concretions.
 */

return [
    /**
     * Set the API variables and credentials
     * @todo overwrite this through admin settings.
     */
    'api_uri'           => '',
    'api_token_uri'     => '',
    'api_client_id'     => '',
    'api_client_secret' => '',

    /**
     * Set the defaults for authentication, http client and api client.
     * @todo overwrite this through admin settings.
     */
    'api.client' => function (Container $container) {
        return $container->get(Client\OpenZaakClient::class);
    },
    'http.client' => function (Container $container) {
        return $container->get(Http\WordPress\WordPressRequestClient::class);
    },
    'api.authenticator' => function (Container $container) {
        return $container->get(Http\Authentication\OpenZaakAuthenticator::class);
    },

    /**
     * API Clients
     */
    Client\OpenZaakClient::class => function (Container $container) {
        return new Client\OpenZaakClient(
            $container->get('http.client'),
            $container->get(Http\Authentication\OpenZaakAuthenticator::class),
        );
    },
    Client\DecosJoinClient::class => function (Container $container) {
        return new Client\DecosJoinClient(
            $container->get('http.client'),
            $container->get(Http\Authentication\DecosJoinAuthenticator::class),
        );
    },
    Client\Client::class => function (Container $container) {
        return $container->get('api.client');
    },

    /**
     * Authenticators
     */
    Http\Authentication\OpenZaakAuthenticator::class => function (Container $container) {
        return new Http\Authentication\OpenZaakAuthenticator(
            $container->get('api_client_id'),
            $container->get('api_client_secret'),
        );
    },
    Http\Authentication\DecosJoinAuthenticator::class => function (Container $container) {
        return new Http\Authentication\DecosJoinAuthenticator(
            $container->get('http.client'),
            $container->get('api_token_uri'),
            $container->get('api_client_id'),
            $container->get('api_client_secret')
        );
    },
    Http\Authentication\TokenAuthenticator::class => function (Container $container) {
        return $container->get('api.authenticator');
    },

    /**
     * HTTP clients
     */
    Http\RequestClientInterface::class => function (Container $container) {
        return $container->get('http.client');
    },
    Http\WordPress\WordPressRequestClient::class => function (Container $container) {
        return new Http\WordPress\WordPressRequestClient(
            new Http\RequestOptions([
                'base_uri'      => $container->get('api_uri'),
                'headers'       => [
                    'Accept-Crs'    => 'EPSG:4326',
                    'Content-Crs'   => 'EPSG:4326',
                    'Content-Type'  => 'application/json',
                ]
            ])
        );
    },
];
