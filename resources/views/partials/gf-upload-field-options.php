<?php

declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\config;

?>

<li class="zgw_upload_setting field_setting">
    <label for="linkedUploadFieldDescriptionZGW" class="section_label">
        <?php esc_html_e('Document beschrijving', config('core.text_domain')); ?>
    </label>
    <input type="text" id="linkedUploadFieldDescriptionZGW" onchange="SetFieldProperty('linkedUploadFieldDescriptionValueZGW', this.value);" />
	<small><?php esc_html_e('Gebruik het ID van een veld om de waarde daarvan te gebruiken (e.g. [id]) in de beschrijving van het bestand.', config('core.text_domain')); ?></small>
</li>
