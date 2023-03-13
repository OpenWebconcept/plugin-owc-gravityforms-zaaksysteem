<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Blocks\Zaken;

use OWC\OpenZaak\Blocks\AbstractBlock;

class Zaken extends AbstractBlock
{
    /*
     * Register block serverside
     */
    public function __construct()
    {
        $this->register('owc/open-zaak', [
            'attributes' => [],
            'render_callback' => [new Block, 'render'],
        ]);
    }
}
