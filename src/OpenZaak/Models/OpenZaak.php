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

    protected function getCurrentStatusTypeNumber(array $types): int
    {
        $currentStatus = $this->getStatusDesc();

        $volgnummer = array_filter($types, function ($type) use ($currentStatus) {
            return $type->getDesc() === $currentStatus;
        });

        if (empty($volgnummer)) {
            return 0;
        }

        $type = reset($volgnummer);

        return $type->getNumber();
    }

    public function getStatusTypes(): array
    {
        $types = $this->data['statusTypes'] ?? [];

        if (empty($types)) {
            return [];
        }

        $volgnummer = $this->getCurrentStatusTypeNumber($types);

        if (!$volgnummer) {
            return $types;
        }

        return array_map(function ($type) use ($volgnummer) {
            if ($type->getNumber() >= $volgnummer) {
                return $type;
            }

            $type->setIsPast(true);
            return $type;
        }, $types);
    }

    public function getFilteredStatusTypes()
    {
        $currentStatus = $this->getStatusDesc();
        $volgnummer = array_filter($this->getStatusTypes(), function ($type) use ($currentStatus) {
            return $type->getDesc() === $currentStatus;
        });
        
        $test = $volgnummer[2]->getNumber();

        $types = array_filter($this->getStatusTypes(), function ($type) use ($test) {
            return $type->getNumber() >= $test;
        });

        return $types;
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
