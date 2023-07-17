<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

class EigenschappenFilter extends AbstractFilter
{
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
