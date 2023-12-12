<?php

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients;

interface ClientInterface
{
    public function zaaktypen(): array;
    public function getClientNamePretty(): string;
}
