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
        $this->register('owc/gravityforms-zaaksysteem', [
            'attributes' => [],
            'render_callback' => [new Block(), 'render'],
        ]);
    }
}
