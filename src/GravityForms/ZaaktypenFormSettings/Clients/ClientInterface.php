<?php

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients;

interface ClientInterface
{
    public function informatieobjecttypen(): array;
    public function zaaktypen(): array;
}
