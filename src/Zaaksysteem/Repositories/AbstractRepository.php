<?php

namespace OWC\Zaaksysteem\Repositories;

use DateTime;

abstract class AbstractRepository
{
    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    public function fieldMapping(array $fields, array $entry): array
    {
        $mappedFields = [];

        foreach ($fields as $field) {
            if (empty($field->linkedFieldValueZGW)) {
                continue;
            }

            $property = rgar($entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ($field->type === 'date') {
                $property = (new DateTime($property))->format('Y-m-d');
            }

            $mappedFields[$field->id] = [
                'eigenschap' => $field->linkedFieldValueZGW,
                'waarde' => $property
            ];
        }

        return $mappedFields;
    }
}
