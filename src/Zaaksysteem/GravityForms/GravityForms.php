<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;
use OWC\Zaaksysteem\Repositories\AbstractRepository;

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
        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $args = $this->handleArgs($instance, $form['fields'], $entry);

        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        return $result;
    }

    protected function handleDecosJoin(array $entry, array $form): array
    {
        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $args = $this->handleArgs($instance, $form['fields'], $entry);

        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        return $result;
    }

    protected function handleEnableU(array $entry, array $form): array
    {
        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $args = $this->handleArgs($instance, $form['fields'], $entry);

        $zaakResult = $instance->createOpenZaak($args);
        $informationObjectResult = $instance->addInformationObjectToZaak($args);
        $connectionResult = $instance->connectZaakToInformationObject($zaakResult, $informationObjectResult);

        return $connectionResult;
    }

    protected function getCreateRepository(): object
    {
        $createRepository = sprintf('OWC\Zaaksysteem\Repositories\%s\CreateZaakRepository', $this->supplier);

        if (! class_exists($createRepository)) {
            throw new \Exception(sprintf('Class %s does not exists', $createRepository));
        }

        return new $createRepository();
    }

    protected function handleArgs(AbstractRepository $instance, array $fields, array $entry)
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

        return $instance->mapArgs($args, $fields, $entry);
    }
}
