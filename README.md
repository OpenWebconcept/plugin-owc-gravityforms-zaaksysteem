# OWC Gravity Forms Zaaksysteem

Combines one or multiple 'zaaksystemen' with Gravity Forms and WordPress.

## Templating

This repository adds a custom WordPress template called `OpenZaak` which:

- Contains the permission logic for accessing the OpenZaak views
- Applies styling to the view and its blocks

## Routing

At the time of writing, two custom routes have been added. These routes enable the following functionalities: displaying a single 'zaak' and downloading information objects attached to a 'zaak.'

### Single Zaak routing

The entity OWC\Zaaksysteem\Entities\Zaak includes a method called 'permalink', which is utilized in various custom Gutenberg blocks. These blocks primarily serve to provide logged-in users with an overview of the 'zaken.' The method 'permalink' returns an URL that is picked up by the added custom routing. (<www.domain.extension/zaak/{identification}/{supplier}>)

This route requires the following conditions:

- A page with 'zaak' as the slug.
- The page should be connected with the 'template-single-zaak.'
- The page should be requested with a 'zaak' identification and a supplier in the URI.

### Downloading Zaak information objects routing

The entity OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject includes a method called 'downloadUrl'. The method 'downloadUrl' returns an URL that is picked up by the added custom routing. (<www.domain.extension/zaak-download/{download-identification}/{zaak-identification}/{supplier}>)

This route requires the following conditions:

- A page with the slug 'zaak-download.' In this case, there is no need for a connected template since there is no page to be opened. The download will initiate in a new tab but will close immediately after the download is completed.
- The page should be requested with an download identification, zaak identification and supplier in the URI.

## Hooks

### Set de lifetime of the form settings transient

A nightly cron job retrieves the form settings per supplier and stores them inside a transient.  
You can adjust the lifetime (in seconds) of that transient using the filter below:

```php
add_filter('owc_gravityforms_zaaksysteem_zaaktypen_form_settings_type_cache_ttl', function(int $ttl){
 return 3600; // 1 hour
})
```

### Filter the GravityForms addon setting fields

Modify or extend the available settings fields in the Gravity Forms add-on by filtering the multi-dimensional settings array:

```php
add_filter('owc_gravityforms_zaaksysteem_gf_settings', function(array $fields){
 return $fields;
});
```

### Configure templates that should be validated for access

Templates listed in this filter will be validated via the template_include hook
to ensure the current session contains a valid BSN or KVK identifier.

```php
add_filter('owc_gravityforms_zaaksysteem_templates_to_validate', function(array $templates){
 $templates[] = 'custom-template';

 return $templates;
});
```
