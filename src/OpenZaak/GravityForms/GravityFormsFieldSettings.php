<?php

namespace OWC\OpenZaak\GravityForms;

use function OWC\OpenZaak\Foundation\Helpers\get_supplier;
use function OWC\OpenZaak\Foundation\Helpers\view;

class GravityFormsFieldSettings
{
    /**
     * Add extra option to Gravity Form fields.
     */
    public function addSelect($position, $formId): void
    {
        if (!class_exists('\GFAPI')) {
            return;
        }

        $form = \GFAPI::get_form($formId);
        $supplier = get_supplier($form);

        if ($position !== 0 || $supplier === 'none') {
            return;
        }

        // Render the supplier based options.
        $mappingOptions = sprintf('partials/gf-field-options-%s.php', $supplier);

        echo view($mappingOptions);
    }
}
