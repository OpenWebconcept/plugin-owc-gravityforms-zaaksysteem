<?php
/**
 * Script for select element
 **/
?>
<script type='text/javascript'>
    // To display custom field under each type of Gravity Forms field.
    jQuery.each(fieldSettings, function(index, value) {
        fieldSettings[index] += ', .highlight_setting_zgw';

		if (index === 'fileupload') {
			fieldSettings[index] += ', .zgw_upload_setting';
		}
    });
    jQuery(document).bind('gform_load_field_settings', function(event, field, form) {
        jQuery('#linkedFieldZGW').val(field['linkedFieldValueZGW']);
		jQuery('#linkedFieldDocumentType').val(field['linkedFieldValueDocumentType']);
		jQuery('#linkedUploadFieldDescriptionZGW').val(field['linkedUploadFieldDescriptionValueZGW']);
    });
</script>
