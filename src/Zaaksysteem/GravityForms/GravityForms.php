<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class GravityForms
{
    protected string $supplier;
    protected string $supplierKey;
    protected bool $hasInformationObject = false;
    
    protected function setSupplier(array $form)
    {
        $this->supplier = get_supplier($form);
        $this->supplierKey = get_supplier($form, true);
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
    protected function handleSupplier(array $entry, array $form): bool
    {
        $handle = sprintf('handle%s', $this->supplier);

        if (! method_exists($this, $handle)) {
            return false;
        }

        return $this->$handle($entry, $form);
    }

    protected function handleEnableU(array $entry, array $form): bool
    {
        try {
            $controller = $this->getSupplierController();
        } catch(\Exception $e) {
            return false;
        }

        return (new $controller($form, $entry, $this->supplierKey))->handle();
    }

    protected function getSupplierController(): string
    {
        $controller = sprintf('OWC\Zaaksysteem\Controllers\%sController', $this->supplier);

        if (! class_exists($controller)) {
            throw new \Exception(sprintf('Class %s does not exists', $controller));
        }

        return $controller;
    }
}
