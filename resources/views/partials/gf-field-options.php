<?php
declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\config;

?>
<li class="label_setting field_setting">
    <label for="linkedFieldZGW" class="section_label">
        <?php _e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>

    <select id="linkedFieldZGW" onchange="SetFieldProperty('linkedFieldValueZGW', this.value);">
        <option value=""><?php _e('Kies veldnaam Zaaksysteem', config('core.text_domain')) ?></option>
        <option value="bronorganisatie"><?php _e('Bronorganisatie', config('core.text_domain')) ?></option>
        <option value="zaaktype"><?php _e('Zaaktype', config('core.text_domain')) ?></option>
        <option value="omschrijving"><?php _e('Omschrijving', config('core.text_domain')) ?></option>
        <option value="toelichting"><?php _e('Toelichting', config('core.text_domain')) ?></option>
        <option value="registratiedatum"><?php _e('Registratiedatum', config('core.text_domain')) ?></option>
        <option value="verantwoordelijkeOrganisatie"><?php _e('Verantwoordelijke organisatie', config('core.text_domain')) ?></option>
        <option value="startdatum"><?php _e('Startdatum', config('core.text_domain')) ?></option>
        <optgroup label="Zaakeigenschappen">
        <?php foreach ($vars['properties'] ?? [] as $property) : ?>
            <option value="<?php echo $property['value']; ?>">
                <?php echo $property['label']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</li>