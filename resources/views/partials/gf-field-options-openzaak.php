<?php
use function OWC\Zaaksysteem\Foundation\Helpers\config;

// The options below are a combination of the `ROL` and `Zaakeigenschappen` api's
?>
<li class="label_setting field_setting">
    <label for="linkedFieldZGW" class="section_label">
        <?php _e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>
    <select id="linkedFieldZGW" onchange="SetFieldProperty('linkedFieldValueZGW', this.value);">
        <option value=""><?php _e('Kies veldnaam OpenZaak', config('core.text_domain')) ?></option>
        <option value="inpBsn"><?php _e('inpBsn', config('core.text_domain')) ?></option>
        <option value="anpIdentificatie"><?php _e('anpIdentificatie', config('core.text_domain')) ?></option>
        <option value="inpA_nummer"><?php _e('inpA_nummer', config('core.text_domain')) ?></option>
        <option value="geslachtsnaam"><?php _e('geslachtsnaam', config('core.text_domain')) ?></option>
        <option value="voorvoegselGeslachtsnaam"><?php _e('voorvoegselGeslachtsnaam', config('core.text_domain')) ?></option>
        <option value="voorletters"><?php _e('voorletters', config('core.text_domain')) ?></option>
        <option value="voornamen"><?php _e('voornamen', config('core.text_domain')) ?></option>
        <option value="any"><?php _e('any', config('core.text_domain')) ?></option>
    </select>
</li>