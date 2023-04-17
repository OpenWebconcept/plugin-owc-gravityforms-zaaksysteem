<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use OWC\Zaaksysteem\Entities\Catalogus;

class ZaaktypenFilter extends AbstractFilter
{
    public function byCatalogus(Catalogus $catalogus)
    {
        return $this->add('catalogus', $catalogus->url);
    }

    // Does not seem to work?
    public function byKeywords(array $keywords)
    {
        return $this->add('trefwoorden', array_filter($keywords, 'is_string'));
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
