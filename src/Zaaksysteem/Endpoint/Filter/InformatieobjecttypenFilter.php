<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use OWC\Zaaksysteem\Entities\Catalogus;

class InformatieobjecttypenFilter extends AbstractFilter
{
    public function byCatalogus(Catalogus $catalogus)
    {
        return $this->add('catalogus', $catalogus->url);
    }

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
