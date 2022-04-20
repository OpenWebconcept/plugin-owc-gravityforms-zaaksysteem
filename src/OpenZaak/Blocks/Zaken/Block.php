<?php declare(strict_types=1);

namespace OWC\OpenZaak\Blocks\Zaken;

use function OWC\OpenZaak\Foundation\Helpers\view;
use OWC\OpenZaak\Repositories\OpenZaakRepository;

class Block
{
    public function render()
    {
        $zaken = (new OpenZaakRepository())->getZaken();
        return view('overview-zaken.php', ['zaken' => $zaken]);
    }
}
