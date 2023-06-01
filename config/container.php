<?php

namespace OWC\Zaaksysteem;

use DI\Container;

/**
 * Link interfaces to their concretions.
 */
return [
    /**
     * OpenZaak configuration
     */
    'openzaak.abbr' => 'oz',
    'oz.client' => fn (Container $container) => $container->get(Client\OpenZaakClient::class),
    'oz.catalogi_url' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-catalogi-url']);
    },
    'oz.documenten_url' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-documenten-url']);
    },
    'oz.zaken_url' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-zaken-url']);
    },
    'oz.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-id']);
    },
    'oz.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-secret']);
    },
    'oz.authenticator' => function (Container $container) {
        return $container->get(Http\Authentication\OpenZaakAuthenticator::class);
    },

    /**
     * Roxit configuration
     */
    'roxit.abbr' => 'ro',
    'ro.client' => fn (Container $container) => $container->get(Client\RoxitClient::class),
    'ro.catalogi_url' => function (Container $container) {
        return $container->make('gf.setting', ['-roxit-catalogi-url']);
    },
    'ro.documenten_url' => function (Container $container) {
        return $container->make('gf.setting', ['-roxit-documenten-url']);
    },
    'ro.zaken_url' => function (Container $container) {
        return $container->make('gf.setting', ['-roxit-zaken-url']);
    },
    'ro.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-roxit-client-id']);
    },
    'ro.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-roxit-client-secret']);
    },
    'ro.authenticator' => function (Container $container) {
        return $container->get(Http\Authentication\RoxitAuthenticator::class);
    },

    /**
     * Decos JOIN configuration
     */
    'decosjoin.abbr' => 'dj',
    'dj.client' => fn (Container $container) => $container->get(Client\DecosJoinClient::class),
    'dj.catalogi_url' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-catalogi-url']);
    },
    'dj.documenten_url' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-documenten-url']);
    },
    'dj.zaken_url' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-zaken-url']);
    },
    'dj.token_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-token-url']);
    },
    'dj.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-id']);
    },
    'dj.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-secret']);
    },
    'dj.client_secret_zrc' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-secret-zrc']);
    },
    'dj.authenticator' => function (Container $container) {
        return $container->get(Http\Authentication\DecosJoinAuthenticator::class);
    },

    /**
     * General configuration
     */
    'rsin' => function (Container $container) {
        return $container->make('gf.setting', ['-rsin']);
    },

    /**
     * Utilize with $container->make('gf.setting', ['setting-name-here']);
     */
    'gf.setting' => function (Container $container, string $type, string $name) {
        return GravityForms\GravityFormsSettings::make()->get($name);
    },

    /**
     * Configure API Clients
     */
    Client\OpenZaakClient::class => function (Container $container) {
        return new Client\OpenZaakClient(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('oz.authenticator'),
        );
    },
    Client\RoxitClient::class => function (Container $container) {
        return new Client\RoxitClient(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('ro.authenticator'),
        );
    },
    Client\DecosJoinClient::class => function (Container $container) {
        return new Client\DecosJoinClient(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('dj.authenticator'),
        );
    },

    /**
     * Authenticators
     */
    Http\Authentication\OpenZaakAuthenticator::class => function (Container $container) {
        return new Http\Authentication\OpenZaakAuthenticator(
            $container->get('oz.client_id'),
            $container->get('oz.client_secret'),
        );
    },
    Http\Authentication\RoxitAuthenticator::class => function (Container $container) {
        return new Http\Authentication\RoxitAuthenticator(
            $container->get('ro.client_id'),
            $container->get('ro.client_secret'),
        );
    },
    Http\Authentication\DecosJoinAuthenticator::class => function (Container $container) {
        return new Http\Authentication\DecosJoinAuthenticator(
            $container->get('dj.client_id'),
            $container->get('dj.client_secret')
        );
    },

    /**
     * HTTP clients
     */
    Http\WordPress\WordPressRequestClient::class => function (Container $container, string $type) {
        return new Http\WordPress\WordPressRequestClient(
            new Http\RequestOptions([
                'headers'       => [
                    'Accept-Crs'    => 'EPSG:4326',
                    'Content-Crs'   => 'EPSG:4326',
                    'Content-Type'  => 'application/json',
                ]
            ])
        );
    },
];
