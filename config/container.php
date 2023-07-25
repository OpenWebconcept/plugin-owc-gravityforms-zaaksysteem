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
    'oz.base_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-url']);
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
    'dj.base_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-url']);
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
                Http\WordPress\WordPressRequestClient::class,
                [$container->get('oz.base_uri')]
            ),
            $container->get('oz.authenticator'),
        );
    },
    Clients\DecosJoin\Client::class => function (Container $container) {
        return new Clients\DecosJoin\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class,
                [$container->get('dj.base_uri')]
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
            $container->get('http.client'),
            $container->get('dj.token_uri'),
            $container->get('dj.client_id'),
            $container->get('dj.client_secret')
        );
    },

    /**
     * HTTP clients
     */
    Http\WordPress\WordPressRequestClient::class => function (Container $container, string $type, string $baseUri) {
        return new Http\WordPress\WordPressRequestClient(
            new Http\RequestOptions([
                'base_uri'      => $baseUri,
                'headers'       => [
                    'Accept-Crs'    => 'EPSG:4326',
                    'Content-Crs'   => 'EPSG:4326',
                    'Content-Type'  => 'application/json',
                ]
            ])
        );
    },
];
