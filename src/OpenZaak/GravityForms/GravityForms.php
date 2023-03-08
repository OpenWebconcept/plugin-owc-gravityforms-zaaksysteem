<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\view;

use OWC\OpenZaak\Repositories\CreateOpenZaakRepository;

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

        $args = [
            'bronorganisatie' => GravityFormsSettings::make()->get('rsin'),
            'verantwoordelijkeOrganisatie' => GravityFormsSettings::make()->get('rsin'),
            //'identificatie' => rgar($entry, '3'), // TODO: not a requirement?
            'zaaktype' => rgar($entry, '4'),
            'startdatum' => rgar($entry, '5'),
            'omschrijving' => rgar($entry, '7'),
        ];

        $instance = new CreateOpenZaakRepository();
        $result = $instance->createOpenZaak($args);

        $instance->createSubmitter($result['url'], rgar($entry, '1.1'));

        if (empty($result)) {
            echo view('form-submission-failed.php');
            exit;
        }

        return $form;
    }
}
