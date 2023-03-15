<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Blocks\Zaken;

use OWC\OpenZaak\Repositories\OpenZaakRepository;

use function OWC\OpenZaak\Foundation\Helpers\view;

class Block
{
    public function render()
    {
        $zaken = (new OpenZaakRepository())->getZaken();
        return view('partials/overview-zaken.php', ['zaken' => $zaken]);
    }
}
