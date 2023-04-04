<?php

use function OWC\Zaaksysteem\Foundation\Helpers\config;

?>
<li class="label_setting field_setting">
    <label for="linkedFieldZGW" class="section_label">
        <?php _e('Zaaksysteem mapping', config('core.text_domain')) ?>
    </label>
    <select id="linkedFieldZGW" onchange="SetFieldProperty('linkedFieldValueZGW', this.value);">
        <option value=""><?php _e('Kies veldnaam Decos Join', config('core.text_domain')) ?></option>
    </select>
</li>