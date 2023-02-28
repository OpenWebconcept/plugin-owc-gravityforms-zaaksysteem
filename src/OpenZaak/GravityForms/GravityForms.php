<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use OWC\OpenZaak\Repositories\CreateOpenZaakRepository;

use function OWC\OpenZaak\Foundation\Helpers\view;

class GravityForms
{
    public function afterSubmission(array $entry, array $form)
    {
        // TODO: should not be hardcoded title
        if (strtolower($form['title']) !== 'openzaak aanmaken' && strtolower($form['title']) !== 'openzaak aanmaken - súdwest-fryslân') {
            return $form;
        }

        $args = [
            'bronorganisatie' => $_ENV['OPEN_ZAAK_RSIN_ORGANIZATION'],
            'verantwoordelijkeOrganisatie' => $_ENV['OPEN_ZAAK_RSIN_ORGANIZATION'],
            //'identificatie' => rgar($entry, '3'), // TODO: not a requirement?
            'zaaktype' => rgar($entry, '4'),
            'startdatum' => rgar($entry, '5'),
            'omschrijving' => rgar($entry, '7'),
        ];

        $instance = new CreateOpenZaakRepository();
        $result = ($instance)->createOpenZaak($args);

        ($instance)->createSubmitter($result['url'], rgar($entry, '1.1'));

        if (empty($result)) {
            echo view('form-submission-failed.php');
            exit;
        }

        return $form;
    }
}
