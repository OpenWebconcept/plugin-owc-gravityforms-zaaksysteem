<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\Repositories\AbstractRepository;

use function OWC\Zaaksysteem\Foundation\Helpers\config;
use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class GravityForms
{
    protected string $supplier;

    protected function setSupplier(array $form)
    {
        $this->supplier = get_supplier($form);
    }

    /**
     * Handle what happens after submitting the form.
     */
    public function afterSubmission(array $entry, array $form)
    {
        $this->setSupplier($form);

        // if there is no supplier set just return the form.
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

    protected function handleOpenZaak(array $entry, $form): array
    {

        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }

        $identifier = $form['owc-gravityforms-zaaksysteem-form-setting-openzaak-identifier'];

        $args = $this->handleArgs($instance, $identifier, $form['fields'], $entry);

        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], \rgar($entry, '1.1'));

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

    protected function handleArgs(AbstractRepository $instance, string $identifier, array $fields, array $entry)
    {
        if (empty(GravityFormsSettings::make()->get('-rsin'))) {
            throw new \Exception(esc_html__('RSIN should not be empty in the Gravity Forms Settings', config('core.text_domain')));
        }

        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('-rsin') ?? '',
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('-rsin') ?? '',
            'zaaktype' => $identifier ?? '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'omschrijving' => '',
            'informatieobject' => ''
        ];

        return $instance->mapArgs($args, $fields, $entry);
    }
}
