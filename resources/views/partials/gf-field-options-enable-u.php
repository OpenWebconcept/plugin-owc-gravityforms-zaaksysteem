<?php
use function OWC\Zaaksysteem\Foundation\Helpers\config;

?>
<li class="label_setting field_setting">
    <label for="linkedFieldZGW" class="section_label">
        <?php _e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>
    <select id="linkedFieldZGW" onchange="SetFieldProperty('linkedFieldValueZGW', this.value);">
        <option value=""><?php _e('Kies veldnaam Enable U', config('core.text_domain')) ?></option>
        <option value="bronorganisatie"><?php _e('Bronorganisatie', config('core.text_domain')) ?></option>
        <option value="zaaktype"><?php _e('Zaaktype', config('core.text_domain')) ?></option>
        <option value="omschrijving"><?php _e('Omschrijving', config('core.text_domain')) ?></option>
        <option value="toelichting"><?php _e('Toelichting', config('core.text_domain')) ?></option>
        <option value="registratiedatum"><?php _e('Registratiedatum', config('core.text_domain')) ?></option>
        <option value="verantwoordelijkeOrganisatie"><?php _e('Verantwoordelijke organisatie', config('core.text_domain')) ?></option>
        <option value="startdatum"><?php _e('Startdatum', config('core.text_domain')) ?></option>
        <option value="informatieobject"><?php _e('Informatieobject', config('core.text_domain')) ?></option>
        <option value="any"><?php _e('any', config('core.text_domain')) ?></option>
        <optgroup label="Zaakeigenschappen">
        <option value="ibanNummer"><?php _e('ibanNummer', config('core.text_domain')) ?></option>
        <option value="startdatumActiviteit"><?php _e('startdatumActiviteit', config('core.text_domain')) ?></option>
        <option value="datumHuwelijkPartnerschap"><?php _e('datumHuwelijkPartnerschap', config('core.text_domain')) ?></option>
        <option value="digitaalAntwoord"><?php _e('digitaalAntwoord', config('core.text_domain')) ?></option>
        </optgroup>
    </select>
</li>