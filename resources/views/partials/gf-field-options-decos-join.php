<?php

use function OWC\Zaaksysteem\Foundation\Helpers\config;

?>
<li class="label_setting field_setting">
    <label for="linkedField" class="section_label">
        <?php _e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>
    <select id="linkedField" onchange="SetFieldProperty('linkedFieldValue', this.value);">
        <option value=""><?php _e('Kies veldnaam Decos Join', config('core.text_domain')) ?></option>
    </select>
</li>