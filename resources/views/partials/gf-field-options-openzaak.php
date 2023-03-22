<?php

use function OWC\Zaaksysteem\Foundation\Helpers\config;

// TODO: check if we can get this list from the OZ api.
?>
<li class="label_setting field_setting">
    <label for="linkedField" class="section_label">
        <?php _e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>
    <select id="linkedField" onchange="SetFieldProperty('linkedFieldValue', this.value);">
        <option value=""><?php _e('Kies veldnaam OpenZaak', config('core.text_domain')) ?></option>
        <option value=""><?php _e('bronorganisatie', config('core.text_domain')) ?></option>
        <option value=""><?php _e('omschrijving', config('core.text_domain')) ?></option>
        <option value=""><?php _e('toelichting', config('core.text_domain')) ?></option>
        <option value=""><?php _e('registratiedatum', config('core.text_domain')) ?></option>
        <option value=""><?php _e('verantwoordelijkeOrganisatie', config('core.text_domain')) ?></option>
        <option value=""><?php _e('startdatum', config('core.text_domain')) ?></option>
        <option value=""><?php _e('any', config('core.text_domain')) ?></option>
    </select>
</li>