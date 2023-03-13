<?php declare(strict_types=1);

namespace OWC\OpenZaak\Blocks\Zaken;

use function OWC\OpenZaak\Foundation\Helpers\get_supplier;
use function OWC\OpenZaak\Foundation\Helpers\view;

class Block
{
    public function render()
    {
        $repository = sprintf('OWC\OpenZaak\Repositories\%s\OpenZaakRepository', get_supplier());
        $zaken = (new $repository())->getZaken();

        return view('overview-zaken.php', ['zaken' => $zaken]);
    }
}
