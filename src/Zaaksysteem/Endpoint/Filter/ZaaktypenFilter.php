<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

class ZaaktypenFilter extends AbstractFilter
{
    // Described as: URL-referentie naar de CATALOGUS waartoe dit ZAAKTYPE behoort.
    // public function byCatalogus(Catalogus $catalogus)
    // {
    //     return $this->add('catalogus', $catalogus->url);
    // }

    // Described as: Unieke identificatie van het ZAAKTYPE binnen
    // de CATALOGUS waarin het ZAAKTYPE voorkomt.
    // public function byIdentifier(string $identifier)
    // {
        // return $this->add('identificatie', $identifier);
    // }

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
