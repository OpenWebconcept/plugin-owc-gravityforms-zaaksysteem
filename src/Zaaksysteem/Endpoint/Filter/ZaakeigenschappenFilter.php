<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use OWC\Zaaksysteem\Entities\Zaak;

class ZaakeigenschappenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }
}
