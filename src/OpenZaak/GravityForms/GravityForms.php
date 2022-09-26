<?php declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\view;
use OWC\OpenZaak\Repositories\CreateOpenZaakRepository;

class GravityForms
{
    public function afterSubmission(array $entry, array $form)
    {
        if (strtolower($form['title']) !== 'openzaak aanmaken') {
            return $form;
        }

        $args = [
            'bronorganisatie' => rgar($entry, '1.1'),
            'verantwoordelijkeOrganisatie' => rgar($entry, '1.1'),
            'identificatie' => rgar($entry, '3'),
            'zaaktype' => rgar($entry, '4'),
            'startdatum' => rgar($entry, '5'),
            'omschrijving' => rgar($entry, '7'),
        ];

        $result = (new CreateOpenZaakRepository())->createOpenZaak($args);

        if (empty($result)) {
            echo view('form-submission-failed.php');
            exit;
        }

        return $form;
    }
}
