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
    'oz.client' => fn (Container $container) => $container->get(Clients\OpenZaak\Client::class),
    'oz.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-catalogi-url']);
    },
    'oz.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-documenten-url']);
    },
    'oz.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-zaken-url']);
    },
    'oz.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-id']);
    },
    'oz.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-secret']);
    },
    'oz.authenticator' => function (Container $container) {
        return $container->get(Clients\OpenZaak\Authenticator::class);
    },

    /**
     * Decos JOIN configuration
     */
    'decosjoin.abbr' => 'dj',
    'dj.client' => fn (Container $container) => $container->get(Clients\DecosJoin\Client::class),
    'dj.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-catalogi-url']);
    },
    'dj.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-documenten-url']);
    },
    'dj.zaken_uri' => function (Container $container) {
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
    'dj.authenticator' => function (Container $container) {
        return $container->get(Clients\DecosJoin\Authenticator::class);
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
    Clients\OpenZaak\Client::class => function (Container $container) {
        return new Clients\OpenZaak\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('oz.authenticator'),
        );
    },
    Clients\DecosJoin\Client::class => function (Container $container) {
        return new Clients\DecosJoin\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('dj.authenticator'),
        );
    },

    /**
     * Authenticators
     */
    Clients\OpenZaak\Authenticator::class => function (Container $container) {
        return new Clients\OpenZaak\Authenticator(
            $container->get('oz.client_id'),
            $container->get('oz.client_secret'),
        );
    },
    Clients\DecosJoin\Authenticator::class => function (Container $container) {
        return new Clients\DecosJoin\Authenticator(
            $container->get('dj.catalogi_url'),
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
