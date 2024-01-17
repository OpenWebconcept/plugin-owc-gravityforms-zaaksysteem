# Changelog

## v2.0.0-beta.1

- Mark dependencies as optional within the configuration file
- Dependency checker now displays informative notice when optional plugins are not activated
- Generate PDF of the submission used to create a 'Zaak' and create Enkelvoudiginformatieobject
- Connect PDF as Zaakinformatieobject to a 'Zaak'
- Fetch information object types and use them for configuring a type per form
- Gutenberg block for displaying 'Zaken'
- Add multiple suppliers (Decos-Join, RxMission and Xxllnc)

## v1.1.0

- Chore: update webpack removes usage of eval

## v1.0.3

- Change: namespace for consistency
- Add: fetch 'zaken' by bsn

## v1.0.2

- Add: 'omschrijving' arg when creating a openzaak

## v1.0.1

- Change: zaken block render method should not be called statically
