<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use function OWC\Zaaksysteem\Foundation\Helpers\view;

class Block
{
    public function render()
    {
        $repository = sprintf('OWC\Zaaksysteem\Repositories\%s\ZaakRepository', 'OpenZaak');
        $zaken = (new $repository())->getZaken();

        return view('partials/overview-zaken.php', ['zaken' => $zaken]);
    }
}
