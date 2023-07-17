<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Resultaattype;
use OWC\Zaaksysteem\Entities\Zaak;

class ResultatenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    public function byResultaattype(Resultaattype $resultaattype)
    {
        return $this->add('resultaattype', $resultaattype->url);
    }
}
