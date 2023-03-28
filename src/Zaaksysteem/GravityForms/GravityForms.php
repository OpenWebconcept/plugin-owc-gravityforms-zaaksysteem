<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class GravityForms
{
    protected string $supplier;
    
    protected function setSupplier(array $form)
    {
        $this->supplier = get_supplier($form);
    }

    public function afterSubmission(array $entry, array $form)
    {
        $this->setSupplier($form);
        
        if (empty($this->supplier)) {
            return $form;
        }

        $result = $this->handleSupplier($entry, $form);

        if (empty($result)) {
            echo view('form-submission-failed.php');
            exit;
        }

        return $form;
    }

    /**
     * Compose method name based on supplier and execute.
     */
    protected function handleSupplier(array $entry, array $form): array
    {
        $handle = sprintf('handle%s', $this->supplier);

        if (! method_exists($this, $handle)) {
            return [];
        }

        return $this->$handle($entry, $form);
    }

    protected function handleOpenZaak(array $entry, array $form): array
    {
        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'zaaktype' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => '',
            'omschrijving' => '',
        ];
        
        $args = $this->mapArgs($args, $form['fields'], $entry);

        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        return $result;
    }

    protected function handleDecosJoin(array $entry, array $form): array
    {
        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'zaaktype' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => '',
            'omschrijving' => '',
        ];
        
        $args = $this->mapArgs($args, $form['fields'], $entry);

        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        return $result;
    }

    protected function handleEnableU(array $entry, array $form): array
    {
        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('-rsin'),
            'zaaktype' => '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => '',
            'omschrijving' => '',
        ];
        
        $args = $this->mapArgs($args, $form['fields'], $entry);

        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $result = $instance->createOpenZaak($args);

        return $result;
    }

    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValues.
     */
    protected function mapArgs(array $args, array $fields, array $entry): array
    {
        foreach ($fields as $field) {
            if (empty($field->linkedFieldValue) || ! isset($args[$field->linkedFieldValue])) {
                continue;
            }

            $property = rgar($entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ($field->type === 'date') {
                $property = (new \DateTime($property))->format('Y-m-d');
            }

            $args[$field->linkedFieldValue] = $property;
        }

        return $args;
    }

    protected function getCreateRepository(): object
    {
        $createRepository = sprintf('OWC\Zaaksysteem\Repositories\%s\CreateZaakRepository', $this->supplier);

        if (! class_exists($createRepository)) {
            throw new \Exception(sprintf('Class %s does not exists', $createRepository));
        }

        return new $createRepository();
    }
}
