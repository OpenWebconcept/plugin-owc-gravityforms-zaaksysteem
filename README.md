# plugin-owc-gravityforms-zaaksysteem

This repo is a POC and is not meant to be used in production as-is.

## Template

This repository adds a custom WordPress template called `OpenZaak` which:

-   Contains the permission logic for accessing the OpenZaak views
-   Applies styling to the view and its blocks

## Routing

At the time of writing, two custom routes have been added. These routes enable the following functionalities: displaying a single 'zaak' and downloading information objects attached to a 'zaak.'

### Single Zaak routing

The entity OWC\Zaaksysteem\Entities\Zaak includes a method called 'permalink', which is utilized in various custom Gutenberg blocks. These blocks primarily serve to provide logged-in users with an overview of the 'zaken.' The method 'permalink' returns an URL that is picked up by the added custom routing. (www.domain.extension/zaak/{identification}/{supplier})

This route requires the following conditions:

-   A page with 'zaak' as the slug.
-   The page should be connected with the 'template-single-zaak.'
-   The page should be requested with a 'zaak' identification and a supplier in the URI.

### Downloading Zaak information objects routing

The entity OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject includes a method called 'downloadUrl'. The method 'downloadUrl' returns an URL that is picked up by the added custom routing. (www.domain.extension/zaak-download/{identification}/{supplier})

This route requires the following conditions:

-   A page with the slug 'zaak-download.' In this case, there is no need for a connected template since there is no page to be opened. The download will initiate in a new tab but will close immediately after the download is completed.
-   The page should be requested with an identification and supplier in the URI.
