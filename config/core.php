<?php

declare(strict_types=1);

return [
    'providers' => [
        OWC\Zaaksysteem\Blocks\BlocksServiceProvider::class,
        OWC\Zaaksysteem\GravityForms\GravityFormsServiceProvider::class,
        OWC\Zaaksysteem\Templating\TemplatingServiceProvider::class,
        OWC\Zaaksysteem\Validation\ValidationServiceProvider::class,
        OWC\Zaaksysteem\Routing\RoutingServiceProvider::class,
        OWC\Zaaksysteem\Http\Logger\LoggerServiceProvider::class,
        OWC\Zaaksysteem\WPCron\WPCronServiceProvider::class,
    ],

    /**
     * Dependencies upon which the plugin relies.
     *
     * Required: type, label
     * Optional: message
     *
     * Type: plugin
     * - Required: file
     * - Optional: version
     *
     * Type: class
     * - Required: name
     */
    'dependencies' => [
        [
            'type' => 'plugin',
            'label' => 'Gravity Forms',
            'version' => '>=2.7.15',
            'file' => 'gravityforms/gravityforms.php',
            'optional' => false,
        ],
        [
            'type' => 'plugin',
            'label' => 'Gravity PDF',
            'version' => '>=6.7.4',
            'file' => 'gravity-pdf/pdf.php',
            'optional' => true,
        ],
    ],

    'text_domain' => OWC_GZ_PLUGIN_SLUG,
];
