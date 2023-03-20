<?php

declare(strict_types=1);

return [
    'providers'    => [
        OWC\OpenZaak\Blocks\BlocksServiceProvider::class,
        OWC\OpenZaak\GravityForms\GravityFormsServiceProvider::class,
        OWC\OpenZaak\Templating\TemplatingServiceProvider::class,
        OWC\OpenZaak\Validation\ValidationServiceProvider::class
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
            'type'    => 'plugin',
            'label'   => 'Gravity Forms',
            'version' => '>=2.5.8',
            'file'    => 'gravityforms/gravityforms.php',
        ],
        [
            'type'    => 'plugin',
            'label'   => 'Yard | GravityForms DigiD',
            'version' => '>=1.0.15',
            'file'    => 'owc-gravityforms-digid/plugin.php',
        ]
    ],

    'text_domain' => 'owc-gravityforms-openzaak',
];
