<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Traits;

use DateTime;
use Exception;
use GF_Field;

trait MergeTags
{
    /**
     * Replaces merge tags in the format [field-id] with corresponding form entry values.
     *
     * This method allows dynamic insertion of field values into a string by parsing and replacing
     * merge tags such as [1]. It is primarily used for upload fields with a
     * configuration option 'Document beschrijving', where users can include references to other field values.
     *
     * Special handling is included for:
     * - Checkbox fields: combines all selected inputs into a single string
     * - Multi-select fields: decodes and joins selected values
     * - Date values: optionally reformats recognized date strings
     */
    public function handleMergeTags(array $entry, array $form, string $value): string
    {
        return preg_replace_callback('/\[[^\]]*\]/', function ($matches) use ($entry, $form) {
            $fieldID = str_replace(['[', ']'], '', $matches[0]);
            $fieldValue = null;

            if ($field = $this->checkFieldType($form, $fieldID, 'checkbox')) {
                $fieldValue = [];

                foreach ($field->inputs as $input) {
                    $fieldValue[] = rgar($entry, $input['id']);
                }
            } elseif ($this->checkFieldType($form, $fieldID, 'multiselect')) {
                $fieldValue = json_decode(rgar($entry, $fieldID), true) ?: [];
            } else {
                $fieldValue = rgar($entry, $fieldID);
            }

            if (is_array($fieldValue)) {
                $fieldValue = $this->formatArrayToString($fieldValue);
            }

            if (! is_string($fieldValue) || empty($fieldValue)) {
                return '';
            }

            $possibleDate = $this->convertPossibleMergeTagDate($fieldValue);

            return ! empty($possibleDate) ? $possibleDate : $fieldValue;
        }, $value);
    }

    protected function checkFieldType(array $form, string $fieldID, string $fieldType): ?GF_Field
    {
        $fields = array_filter($form['fields'], function ($field) use ($fieldID, $fieldType) {
            return $field->id == $fieldID && $fieldType === $field->type;
        });

        $field = reset($fields);

        return $field instanceof GF_Field ? $field : null;
    }

    protected function formatArrayToString(array $items): ?string
    {
        $items = array_filter($items, fn ($item) => trim($item) !== '');

        if (empty($items)) {
            return null;
        }

        if (count($items) === 1) {
            return reset($items);
        }

        $lastItem = array_pop($items);

        return implode(', ', $items) . ' en ' . $lastItem;
    }

    protected function convertPossibleMergeTagDate(string $value, string $format = 'd-m-Y'): string
    {
        // A valid date string has a length of 10.
        if (strlen($value) !== 10) {
            return '';
        }

        try {
            $date = new DateTime($value);
        } catch (Exception $e) {
            return '';
        }

        return $date->format($format);
    }
}
