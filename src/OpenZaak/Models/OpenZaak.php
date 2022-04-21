<?php declare(strict_types=1);

namespace OWC\OpenZaak\Models;

class OpenZaak
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getURL(): string
    {
        return $this->data['url'] ?? '';
    }

    public function getStatusURL()
    {
        return $this->data['status'] ?? '';
    }

    public function getDateStatusAssigned(): string
    {
        $date = $this->data['datumStatusGezet'] ?? '';

        if (empty($date)) {
            return '';
        }

        return (new \DateTime($date))->format('d-m-Y');
    }

    public function setDateStatusAssigned(string $date = ''): self
    {
        $this->data['datumStatusGezet'] = $date;

        return $this;
    }

    public function getStatusTypeURL(): string
    {
        return $this->data['statusTypeURL'] ?? '';
    }

    public function setStatusTypeURL(string $url = ''): self
    {
        $this->data['statusTypeURL'] = $url;

        return $this;
    }

    public function getDesc(): string
    {
        return $this->data['omschrijving'] ?? '';
    }

    public function getStatusDesc(): string
    {
        return $this->data['status_omschrijving'] ?? 'Niet beschikbaar';
    }

    public function setStatusDesc(string $desc = ''): self
    {
        $this->data['status_omschrijving'] = $desc;

        return $this;
    }

    public function getStatusTypes(): array
    {
        return $this->data['statusTypes'] ?? [];
    }

    public function setStatusTypes(array $types): self
    {
        $this->data['statusTypes'] = $types;

        return $this;
    }

    public function getIdentification(): string
    {
        return $this->data['identificatie'] ?? '';
    }

    public function getTypeURL(): string
    {
        return $this->data['zaaktype'] ?? '';
    }

    public function getStartDate(): string
    {
        $date = $this->data['startdatum'] ?? '';

        if (empty($date)) {
            return '';
        }

        return (new \DateTime($date))->format('d-m-Y');
    }

    public function getRegistrationDate(): string
    {
        $registrationDate = $this->data['registratiedatum'] ?? '';

        if (empty($registrationDate)) {
            return '';
        }

        return (new \DateTime($registrationDate))->format('d-m-Y');
    }
}
