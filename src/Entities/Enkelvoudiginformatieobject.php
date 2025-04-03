<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

use DateTime;
use Exception;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use OWC\Zaaksysteem\Traits\ZaakIdentification;

class Enkelvoudiginformatieobject extends Entity
{
    use ZaakIdentification;

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

    public function creationDate(): string
    {
        $date = $this->getValue('creatiedatum', '');

        if (empty($date)) {
            return '';
        }

        try {
            return (new DateTime($date))->format('d-m-Y');
        } catch (Exception $e) {
            return '';
        }
    }

    public function formatType(): string
    {
        $type = $this->getValue('formaat', '');

        if (empty($type)) {
            return '';
        }

        $mimeMap = resolve('mime.mapping');

        return $mimeMap[$type] ?? '';
    }

    public function formattedMetaData(): string
    {
        $meta = array_filter([
            $this->formatType(),
            $this->sizeFormatted(),
            $this->creationDate(),
        ]);

        if (empty($meta)) {
            return '';
        }

        return implode(', ', $meta);
    }

    public function downloadUrl(string $zaakIdentification): string
    {
        if ($this->isClassified() || ! $this->hasFinalStatus()) {
            return '';
        }

        $identification = $this->identification();

        if (empty($identification) || empty($zaakIdentification)) {
            return '';
        }

        return sprintf('%s/zaak-download/%s/%s/%s', get_site_url(), $identification, $this->encodeZaakIdentification($zaakIdentification), $this->getClientNamePretty());
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

    public function status(): string
    {
        return $this->getValue('status', '');
    }

    public function hasFinalStatus(): bool
    {
        if ($this->hasReceiptDate()) {
            return true;
        }

        $status = $this->status();

        $finalStatusses = [
            'definitief',
            'gearchiveerd',
        ];

        return in_array($status, $finalStatusses);
    }

    public function hasReceiptDate(): bool
    {
        return ! empty($this->getValue('ontvangstdatum', ''));
    }

    public function confidentialityDesignation(): string
    {
        return $this->getValue('vertrouwelijkheidaanduiding', '');
    }

    public function isCaseConfidential(): bool
    {
        $designation = $this->confidentialityDesignation();

        return 'zaakvertrouwelijk' === $designation;
    }

    public function isConfidential(): bool
    {
        $designation = $this->confidentialityDesignation();

        return 'vertrouwelijk' === $designation;
    }

    public function displayAllowedByConfidentialityDesignation(): bool
    {
        $designation = $this->confidentialityDesignation();

        $allowedDesignations = [
            'openbaar',
            'beperkt_openbaar',
            'intern',
            'zaakvertrouwelijk',
        ];

        return in_array(strtolower($designation), $allowedDesignations, true);
    }

    public function isClassified(): bool
    {
        $designation = $this->confidentialityDesignation();
        $classifiedDesignations = [
            'intern',
            'confidentieel',
            'geheim',
            'zeer_geheim',
        ];

        return in_array($designation, $classifiedDesignations);
    }
}
