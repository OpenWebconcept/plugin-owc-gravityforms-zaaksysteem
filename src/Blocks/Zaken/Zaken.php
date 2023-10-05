<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use OWC\Zaaksysteem\Blocks\AbstractBlock;

class Zaken extends AbstractBlock
{
    /**
     * Register block server side.
     */
    public function __construct()
    {
        $this->register('owc/mijn-zaken', [
            'attributes' => [
                'zaakClient' => [
                    'type' => 'string',
                    'default' => 'openzaak',
                ],
                'zaaktypeFilter' => [
                    'type'    => 'string',
                    'default' => [],
                    'items'   => [
                        'type' => 'object'
                    ]
                ],
                'updateMePlease' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'combinedClients' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'byBSN' => [
                    'type' => 'boolean',
                    'default' => true
                ],
                'view' => [
                    'type' => 'string',
                    'default' => 'default',
                ],
                'title' => 'Mijn Zaken',
                'style' => 'swf-extended-blocks-style',
                'editor_script' => 'swf-extended-blocks-script',
                'editor_style' => 'swf-extended-blocks-editor-style',
            ],
            'render_callback' => [new Block(), 'render'],
        ]);
    }
}
