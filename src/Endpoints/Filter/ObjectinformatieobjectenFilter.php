<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Zaak;

class ObjectinformatieobjectenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('object', $zaak->url);
    }
}
