# Changelog

## v2.7.0

- Change: update 'Procura' labels to 'Shift2'
- Fix: missing validations while retrieving 'zaak' properties and objecttypes
- Add: implement supplier Mozart
- Fix: OpenWave container definition keys

## v2.6.1

- Fix: Implement validation logic during entity mapping and attribute hydration.

## v2.6.0

- Feat: implement supplier OpenWave + optimalizations in form settings
- Change: mime mapping array from DI container
- Change: improve validation when downloading a 'zaak' informationobject
- Change: rewrite rule zaak information-object download supports '.' in zaak identification
- Feat: add 'zaak' document/information-object descriptions

## v2.5.0

- Refactor: remove unused applying of ssl certs in abstract client class
- Chore: zaak failure messages
- Chore: update npm dependencies

## v2.4.2

- Fix: gravity-pdf dependency has different folder name now

## v2.4.1

- Refactor: check receipt date of information object first before checking final status
- Fix: validation when applying ssl certificates
- Feat: add displayAllowedByConfidentialityDesignation validation method in Enkelvoudiginformatieobject class

## v2.4.0

- Feat: validate protected templates with eHerkenning

## v2.3.3

- Refactor: handle failure of retrieving single entity in ResourceCollection class

## v2.3.2

- Feat: encode/decode slashes in zaak identification in Enkelvoudiginformatieobject

## v2.3.1

- Refactor: improve validation on status explanation
- Refactor: encode/decode slashes in zaak identification, ensure routing system compatibility
- Fix: translations just in time error

## v2.3.0

- Feat: add supplier selection
- Feat: filter options on enabled suppliers

## v2.2.2

- Fix: check if DigiD userdata is not null before retrieving BSN

## v2.2.1

- Feat: retrieve zaaktype eigenschappen paginated in form settings

## v2.2.0

- Chore: add fallback for SAML digid plugin

## v2.1.1

- Refactor: php8 syntax to php7 syntax

## v2.1.0

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
