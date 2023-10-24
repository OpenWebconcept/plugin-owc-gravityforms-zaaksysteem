# TO-DO

- Retrieve substatussen and add to Zaak object.
- Translations.
- When creating a Zaak, via form submission, add extra fields.
- Retrieve 'ZaakTypen' dynamically and use it in form settings.
- Map form fields values to args used in request for creating zaak.
- Remove OWC\Zaaksysteem\Providers\MijnZakenProvider::class when block is finished inside the src/Blocks/Zaken dir.

## Decos Join

This plugin should also support an implementation of Decos for creating 'zaken'

- Decos uses another payload for generating a token. -> https://zgw-ztc-api-acc.decosasp.com/swagger/index.html#/Token/Token_Token
- Compare the current proces of creating 'zaken' -> https://stufsuite-jzd-acc.decosasp.com/zrcui/swaggerui/index#!/Zaken/Zaken_CreateAsync_0
- Compare the current proces of retrieving 'zaken' -> https://stufsuite-jzd-acc.decosasp.com/zrcui/swaggerui/index#!/Zaken/Zaken_ListAsync_0

It's possible that it is required to isolate the code of the 'zaken' by provider. So isolated code for the OpenZaak implementation and the same for Decos Join.

## Enable-u

- Add BSN to request
- Add logging when errors occure.

/src/Zaaksysteem/Repositories/EnableU/CreateZaakRepository.php:88

- Author should be fetched dynamically

/src/Zaaksysteem/Repositories/EnableU/CreateZaakRepository.php:91

- informatieobjecttype should be fetched dynamically someday.
