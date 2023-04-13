<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use OWC\Zaaksysteem\Entities\Zaak;

class ZaakinformatieobjectenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    // public function byInformatieobject(Informatieobject $informatieobject)
    // {
    //     return $this->add('informatieobject', $informatieobject->url);
    // }
}
