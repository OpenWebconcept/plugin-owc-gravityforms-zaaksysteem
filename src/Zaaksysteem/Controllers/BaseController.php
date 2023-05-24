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

        $args = $this->mapArgs($args);
        $args['omschrijving'] = $this->convertMergeTags($args['omschrijving']);
        
        return $args;
    }

    /**
     * Convert merge tags to the value of the corrensponding fields.
     * Search for field IDs between square brackets like: [3].
     * Retrieve field value based on the ID and return instead of found match.
     */
    protected function convertMergeTags(string $value): string
    {
        return preg_replace_callback('/\[[^\]]*\]/', function ($matches) {
            $fieldID = str_replace(['[', ']'], '', $matches[0]);

            return rgar($this->entry, $fieldID);
        }, $value);
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

            $fieldValue = rgar($this->entry, (string)$field->id);

            if (empty($fieldValue)) {
                continue;
            }

            if ($field->type === 'date') {
                $fieldValue = (new \DateTime($fieldValue))->format('Y-m-d');
            }

            if ($field->linkedFieldValueZGW === 'informatieobject') {
                $args = $this->mapInformationObjectArg($args, $field, $fieldValue);

                continue;
            }

            $args[$field->linkedFieldValueZGW] = $fieldValue;
        }

        return $args;
    }

    /**
     * Fields mapped to 'informatieobject' can contain a simple url but also an array of urls in JSON format.
     */
    protected function mapInformationObjectArg(array $args, $field, $fieldValue): array
    {
        $start = substr($fieldValue, 0, 1);
        $end = substr($fieldValue, -1, 1);

        // Check if field value is an array in JSON format and decode.
        if ($start === '[' && $end === ']') {
            $fieldValue = json_decode($fieldValue);
        }

        if (is_string($fieldValue)) {
            $fieldValue = [$fieldValue];
        }

        if (! empty($args[$field->linkedFieldValueZGW])) {
            $args[$field->linkedFieldValueZGW] = array_merge($args[$field->linkedFieldValueZGW], $fieldValue);
        } else {
            $args[$field->linkedFieldValueZGW] = $fieldValue;
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

            $digiDFieldID = sprintf('%d.1', $field->id);

            break;
        }

        return $digiDFieldID ? rgar($this->entry, $digiDFieldID) : '';
    }
}
