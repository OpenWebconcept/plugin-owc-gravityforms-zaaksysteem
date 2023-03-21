<?php

use function OWC\OpenZaak\Foundation\Helpers\config;

?>
<li class="label_setting field_setting">
    <label for="linkedField" class="section_label">
        <?php _e('OpenZaak mapping', config('core.text_domain')) ?>
    </label>
    <select id="linkedField" onchange="SetFieldProperty('linkedFieldValue', this.value);">
        <option value=""><?php _e('Kies veldnaam Maykin', config('core.text_domain')) ?></option>
    </select>
</li>