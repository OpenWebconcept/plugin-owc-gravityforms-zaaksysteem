<?php

namespace OWC\Zaaksysteem\Controllers;

use OWC\Zaaksysteem\GravityForms\GravityFormsSettings;

class BaseController
{
    protected array $form;
    protected array $entry;
    protected bool $hasInformationObject = false;

    public function __construct(array $form, array $entry)
    {
        $this->form = $form;
        $this->entry = $entry;
    }
    
    protected function handleArgs()
    {
        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'zaaktype' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => '',
            'omschrijving' => '',
            'informatieobject' => ''
        ];

        return $this->mapArgs($args);
    }

    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    protected function mapArgs(array $args): array
    {
        foreach ($this->form['fields'] as $field) {
            if (empty($field->linkedFieldValueZGW) || ! isset($args[$field->linkedFieldValueZGW])) {
                continue;
            }

            $property = rgar($this->entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ($field->type === 'date') {
                $property = (new \DateTime($property))->format('Y-m-d');
            }

            $args[$field->linkedFieldValueZGW] = $property;
        }

        return $args;
    }


    /**
     * Validate if form has a DigiD field.
     * If so return the decrypted BSN number.
     */
    protected function getBSN(): string
    {
        $digiDFieldID = '';

        foreach ($this->form['fields'] as $field) {
            if ($field->type !== 'digid') {
                continue;
            }

            $digiDFieldID = $field->id;

            break;
        }

        return $digiDFieldID ? rgar($this->entry, $digiDFieldID) : '';
    }
}
