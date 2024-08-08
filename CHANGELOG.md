# Changelog

## v2.1

- Feat: add Procura as supplier
- Feat: implement SSL certificate application based on client requirements
- Feat: store the generated zaak URL in the entry's metadata for future reference after form submission
- Refactor: get first enabled PDF form setting
- Refactor: remove version argument from PDF submission args since it is not used
- Feat: scheduled a WP_Cron event to automatically delete ‘Zaken’ of entries where a payment was required but the payment status has been pending for an extended period.

## v2.0.1

- Chore: disable sslverify again inside CreateZaakAction (Decos)

## v2.0.0

- Chore: remove readspeaker from plug-in views
- Fix: multiple catch statements in AbstractCreateZaakAction class
- Refactor: use roltype property 'omschrijvingGeneriek' as 'roltoelichting' when creating 'Zaak'
- Feat: mark the first proces status of a 'Zaak' as current when a 'Zaak' does not have a active status
- Feat: ensure uniform usage of 'volgnummers' across different clients
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
