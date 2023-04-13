<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Attributes\ObjectType;

class ZaakobjectenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    // public function byObject(Object $object)
    // {
    //     return $this->add('object', $object->url);
    // }

    public function byObjectType(ObjectType $objectType)
    {
        return $this->add('objecttype', $objectType->get());
    }
}
