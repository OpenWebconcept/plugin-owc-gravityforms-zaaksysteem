<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Traits;

use GFAPI;

trait EntryNote
{
    public function entryAddNote($entryId, string $message, string $type = 'error'): void
    {
        GFAPI::add_note($entryId, 0, \OWC_GZ_SHORT_NAME, $message, 'user', $type);
    }
}
