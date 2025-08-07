<?php
declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\config;

?>
<li class="label_setting field_setting">
    <label for="linkedFieldZGW" class="section_label">
        <?php esc_html_e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>

    <select id="linkedFieldZGW" onchange="SetFieldProperty('linkedFieldValueZGW', this.value);">
        <option value=""><?php esc_html_e('Kies veldnaam Zaaksysteem', config('core.text_domain')) ?></option>
        <option value="bronorganisatie"><?php esc_html_e('Bronorganisatie', config('core.text_domain')) ?></option>
        <option value="zaaktype"><?php esc_html_e('Zaaktype', config('core.text_domain')) ?></option>
        <option value="omschrijving"><?php esc_html_e('Omschrijving', config('core.text_domain')) ?></option>
        <option value="toelichting"><?php esc_html_e('Toelichting', config('core.text_domain')) ?></option>
        <option value="registratiedatum"><?php esc_html_e('Registratiedatum', config('core.text_domain')) ?></option>
        <option value="verantwoordelijkeOrganisatie"><?php esc_html_e('Verantwoordelijke organisatie', config('core.text_domain')) ?></option>
        <option value="startdatum"><?php esc_html_e('Startdatum', config('core.text_domain')) ?></option>
		<option value="informatieobject"><?php esc_html_e('Informatieobject', config('core.text_domain')) ?></option>
        <optgroup label="Zaakeigenschappen">
        <?php foreach ($vars['properties'] ?? [] as $property) : ?>
            <option value="<?php echo $property['value']; ?>">
                <?php echo $property['label']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</li>
<li class="label_setting field_setting">
    <label for="linkedFieldDocumentType" class="section_label">
        <?php esc_html_e('Document typen', config('core.text_domain')) ?>
    </label>
	<select id="linkedFieldDocumentType" onchange="SetFieldProperty('linkedFieldValueDocumentType', this.value);">
		<option value=""><?php esc_html_e('Kies een document type', config('core.text_domain')) ?></option>
		<?php foreach ($vars['objecttypes'] ?? [] as $property) : ?>
			<option value="<?php echo $property['value']; ?>">
					<?php echo $property['label']; ?>
				</option>
		<?php endforeach; ?>
	</select>
</li>
