<?php

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\view;

class GravityFormsFieldSettings
{
    /**
     * Add extra option to Gravity Form fields.
     */
    public function addSelect($position): void
    {
        if ($position !== 0) {
            return;
        }

        echo view('partials/gf-field-option.php');
    }
}
