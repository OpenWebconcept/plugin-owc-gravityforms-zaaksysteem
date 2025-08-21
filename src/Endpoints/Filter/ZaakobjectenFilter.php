<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Attributes\ObjectType;
use OWC\Zaaksysteem\Entities\Zaak;

class ZaakobjectenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    /**
     * @todo Figure out what a 'Object' is as referenced here:
     * https://test.openzaak.nl/zaken/api/v1/schema/#operation/zaakobject_list
     */
    // public function byObject(Object $object)
    // {
    //     return $this->add('object', $object->url);
    // }

    public function byObjectType(ObjectType $objectType)
    {
        return $this->add('objecttype', $objectType->get());
    }
}
