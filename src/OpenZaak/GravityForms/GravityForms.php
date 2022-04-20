<?php declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

class GravityForms
{
    public function afterSubmission(array $entry, array $form)
    {
        print_r($entry);
        die;
    }
}
