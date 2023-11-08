<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Enkelvoudiginformatieobject extends Entity
{
    protected array $casts = [
        // url
        // identificatie
        // bronorganisatie
        // creatiedatum
        // titel
        // vertrouwelijkheidaanduiding
        // auteur
        // status
        // formaat
        // taal
        // versie
        // beginRegistratie
        // bestandsnaam
        // inhoud
        // bestandsomvang
        // link
        // beschrijving
        // ontvangstdatum
        // verzenddatum
        // indicatieGebruiksrecht
        // ondertekening
        // integriteit
        // informatieobjecttype
        // locked
        // bestandsdelen
    ];

    public function title(): string
    {
        return $this->getValue('titel', '');
    }

    public function fileName(): string
    {
        return $this->getValue('bestandsnaam', '');
    }

    public function content(): string
    {
        return $this->getValue('inhoud', '');
    }

    public function language(): string
    {
        return $this->getValue('taal', '');
    }

    public function sizeFormatted(): string
    {
        $size = $this->size();

        return $size ? size_format($size) : '';
    }

    public function size(): int
    {
        return $this->getValue('bestandsomvang', 0);
    }

    public function downloadUrl(): string
    {
        $identification = $this->identification();

        if (empty($identification)) {
            return '';
        }

        return sprintf('%s/zaak-download/%s/%s', get_site_url(), $identification, $this->getClientNamePretty());
    }

    protected function identification(): string
    {
        $url = $this->url();

        if (empty($url)) {
            return '';
        }

        $parts = explode('/', $url);

        return end($parts) ?: '';
    }

    public function url(): string
    {
        return $this->getValue('url', '');
    }
}
