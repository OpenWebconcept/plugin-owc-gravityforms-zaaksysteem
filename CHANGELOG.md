# Changelog

## v2.0.0-beta.1

- Feat: enable filtering on orderBy in gutenberg block
- Feat: manual configuration of form settings, besides selecting retrieved settings from external source
- Refactor: general improvements and cleaning-up
- Feat: support uploads made by resident by using upload form fields
- Feat: add Collection class method take()
- Feat: handle and display errors after creating a 'Zaak'
- Feat: Zaken block, view with current Zaken only. Amount of items is configurable
- Feat: download all types of Zaak information objects instead of hardcoded PDF support only
- Feat: display additional meta of information object connect to a Zaak
- Feat: mark dependencies as optional within the configuration file
- Feat: dependency checker now displays informative notice when optional plugins are not activated
- Feat: generate PDF of the submission used to create a 'Zaak' and create Enkelvoudiginformatieobject
- Feat: connect PDF as Zaakinformatieobject to a 'Zaak'
- Feat: fetch information object types and use them for configuring a type per form
- Feat: gutenberg block for displaying 'Zaken'
- Feat: add multiple suppliers (Decos-Join, RxMission and Xxllnc)

## v1.1.0

- Chore: update webpack removes usage of eval

## v1.0.3

- Change: namespace for consistency
- Add: fetch 'zaken' by bsn

## v1.0.2

- Add: 'omschrijving' arg when creating a openzaak

## v1.0.1

- Change: zaken block render method should not be called statically
