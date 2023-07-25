<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Zaaktype;

class RoltypenFilter extends AbstractFilter
{
    public function byZaaktype(Zaaktype $zaaktype)
    {
        return $this->add('zaaktype', $zaaktype->uuid);
    }

    // public function byGenericDescription(string $rolDescription)
    // {
    //     // @todo...

    //     return $this->add('omschrijvingGeneriek', $rolDescription->get());
    // }

    public function byStatusConcept()
    {
        return $this->add('status', 'concept');
    }

    public function byStatusDefinitief()
    {
        return $this->add('status', 'definitief');
    }

    public function byStatusAlles()
    {
        return $this->add('status', 'alles');
    }
}
