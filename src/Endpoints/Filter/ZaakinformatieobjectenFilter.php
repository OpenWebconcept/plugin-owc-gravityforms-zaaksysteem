<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Zaak;

class ZaakinformatieobjectenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    /**
     * @todo Figure out what a 'Informatieobject' is as referenced here:
     * https://test.openzaak.nl/zaken/api/v1/schema/#operation/zaakinformatieobject_list
     */
    // public function byInformatieobject(Informatieobject $informatieobject)
    // {
    //     return $this->add('informatieobject', $informatieobject->url);
    // }
}
