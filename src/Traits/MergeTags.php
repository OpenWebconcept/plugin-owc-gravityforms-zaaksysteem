<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Traits;

use DateTime;
use Exception;
use GF_Field;

trait MergeTags
{
    public function handleMergeTags(array $entry, array $form, string $value)
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

    /**
     * When a merge tag contains a date, convert to given format.
     */
    protected function convertPossibleMergeTagDate(string $value, string $format = 'd-m-Y'): string
    {
        /**
         * A valid date string has a length of 10.
         * Housenumber additions could be 'B' for example, 'B' also corrensponds with a timezone.
         */
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
