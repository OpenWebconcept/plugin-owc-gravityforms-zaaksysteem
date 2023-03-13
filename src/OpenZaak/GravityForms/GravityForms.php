<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\get_supplier;
use function OWC\OpenZaak\Foundation\Helpers\view;

class GravityForms
{
    public function afterSubmission(array $entry, array $form)
    {
        /**
         * TODO: should not be hardcoded title.
         * Maybe add checkbox to form settings, check down below if form has been checked.
         */
        if (strtolower($form['title']) !== 'openzaak aanmaken' && strtolower($form['title']) !== 'openzaak aanmaken - súdwest-fryslân') {
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
        $handle = sprintf('handle%s', get_supplier());

        return $this->$handle($entry, $form);
    }

    protected function handleMaykinMedia(array $entry, array $form): array
    {
        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('rsin'),
            //'identificatie' => rgar($entry, '3'), // TODO: not a requirement?
            'zaaktype' => rgar($entry, '4'),
            'startdatum' => rgar($entry, '5'),
            'omschrijving' => rgar($entry, '7'),
        ];

        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }
        
        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        return $result;
    }

    protected function handleDecos(array $entry, array $form): array
    {
        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('rsin'),
            //'identificatie' => rgar($entry, '3'), // TODO: not a requirement?
            'zaaktype' => rgar($entry, '4'),
            'startdatum' => rgar($entry, '5'),
            'omschrijving' => rgar($entry, '7'),
        ];

        try {
            $instance = $this->getCreateRepository();
        } catch(\Exception $e) {
            return [];
        }
        
        $result = $instance->createOpenZaak($args);
        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        return $result;
    }

    protected function getCreateRepository(): object
    {
        $createRepository = sprintf('OWC\OpenZaak\Repositories\%s\CreateOpenZaakRepository', get_supplier());

        if (! class_exists($createRepository)) {
            throw new \Exception(sprintf('Class %s does not exists', $createRepository));
        }

        return new $createRepository();
    }
}
