<?php

namespace OWC\Zaaksysteem;

use DI\Container;

/**
 * Link interfaces to their concretions.
 */
return [
    /**
     * OpenZaak configuration.
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
     * Decos JOIN configuration.
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
    'dj.client_secret_zrc' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-secret-zrc']);
    },
    'dj.authenticator' => function (Container $container) {
        return $container->get(Clients\DecosJoin\Authenticator::class);
    },

    /**
     * RX.Mission configuration.
     */
    'rx-mission.abbr' => 'rx',
    'rx.client' => fn (Container $container) => $container->get(Clients\RxMission\Client::class),
    'rx.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-catalogi-url']);
    },
    'rx.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-documenten-url']);
    },
    'rx.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-zaken-url']);
    },
    'rx.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-client-id']);
    },
    'rx.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-client-secret']);
    },
    'rx.authenticator' => function (Container $container) {
        return $container->get(Clients\RxMission\Authenticator::class);
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
            $container->get('oz.zaken_uri'),
            $container->get('oz.catalogi_uri'),
            $container->get('oz.documenten_uri'),
        );
    },

    Clients\DecosJoin\Client::class => function (Container $container) {
        return new Clients\DecosJoin\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('dj.authenticator'),
            $container->get('dj.zaken_uri'),
            $container->get('dj.catalogi_uri'),
            $container->get('dj.documenten_uri'),
        );
    },

    Clients\RxMission\Client::class => function (Container $container) {
        return new Clients\RxMission\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('rx.authenticator'),
            $container->get('rx.zaken_uri'),
            $container->get('rx.catalogi_uri'),
            $container->get('rx.documenten_uri'),
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
            $container->get('dj.client_id'),
            $container->get('dj.client_secret')
        );
    },

    Clients\RxMission\Authenticator::class => function (Container $container) {
        return new Clients\RxMission\Authenticator(
            $container->get('rx.client_id'),
            $container->get('rx.client_secret')
        );
    },

    /**
     * HTTP clients
     */
    Http\WordPress\WordPressRequestClient::class => function (Container $container, string $type) {
        return new Http\WordPress\WordPressRequestClient(
            new Http\RequestOptions([
                'timeout' => 10,
                'headers' => [
                    'Accept-Crs'    => 'EPSG:4326',
                    'Content-Crs'   => 'EPSG:4326',
                    'Content-Type'  => 'application/json',
                ]
            ])
        );
    },
];
