<?php

namespace OWC\Zaaksysteem\Traits;

use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

trait ResolveBSN
{
    /**
     * Resolves the BSN from the current sessions.
     */
    protected function resolveCurrentBsn(): string
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn');

        return ! empty($bsn) && is_string($bsn) ? decrypt($bsn) : '';
    }
}
