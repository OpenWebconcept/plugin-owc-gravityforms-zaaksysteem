<?php declare(strict_types=1);

namespace OWC\OpenZaak\Models;

class OpenZaak
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getIdentification(): string
    {
        return $this->data['identificatie'] ?? '';
    }

    public function getStartDate(): string
    {
        return $this->data['startdatum'] ?? '';
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
