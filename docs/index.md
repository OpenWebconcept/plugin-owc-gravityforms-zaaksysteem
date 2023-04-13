
# OpenZaak API

## Configuration
Check out `config/container.php` and adjust variables as needed. 

## Accessing an Endpoint

```php
namespace OWC\Zaaksysteem;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

// $client is an instance of \OWC\Zaaksysteem\Client\Client 
// and is (currently!) configured in /config/container.php.
$client = resolve('api.client');

// Optionally check if the client supports a given Endpoint:
if ($client->supports('zaken') && $client->supports('zaakobjecten') {
    // Do something, e.g. enable some piece of functionality
}

// Get any of the supported endpoints from the API driver.
$zakenEndpoint = $client->zaken();

// Find a specific zaak. Returns a \OWC\Zaaksysteem\Entities\Zaak
$zaak = $zakenEndpoint->find($zaakUuid);

```

All single objects returned from an Endpoint are an instance of the `OWC\Zaaksysteem\Entities\Entity` class. This class allows easy access to properties, while also allowing properties to be cast to certain types. Additionally, helper methods are implemented to cast the model to an array or json.

When a method returns a list of entities, an instance of `OWC\Zaaksysteem\Support\Collection` is returned. This class functions like an array, but also has helper methods like `map()`, `isEmpty()` and `filter()`. It also allows sorting through `sort()`, `asort()` and `sortByAttribute()`.

Most endpoints return a `PagedCollection` which extends the default `Collection`. It add access to pagination variables through the `pageMeta()` method.
```php
// Get all zaken. Returns a \OWC\Zaaksysteem\Support\PagedCollection instance
$zaken = $zakenEndpoint->all();
if ($zaken->isEmpty()) {
    // Do something
}

if ($zaken->pageMeta()->hasNextPage()) {
    // Load the next page, for example:
    $filter = new \OWC\Zaaksysteem\Endpoint\Filter\ZakenFilter();
    $filter->page($zaken->pageMeta()->getNextPageNumber());
    $zaken = $zakenEndpoint->filter($filter);
}
```

It's also possible to apply a filter on other attributes.
```php
// Apply a filter. Start with creating a new ZakenFilter instance.
$filter = new \OWC\Zaaksysteem\Endpoint\Filter\ZakenFilter();

// The available methods differ to every AbstractFilter implementation.
$filter->byStartDate(new DateTime());
// Though on every filter attributes can be set through the `add()` method.
$filter-add('attribute', 'value');
// Returns a \OWC\Zaaksysteem\Support\PagedCollection instance.
$zaken = $zakenEndpoint->filter($filter);
```

## Lazy loadable resources

Connected resources are not automatically loaded, but only when accessed.

```php
// We'll start with finding a specific zaak.
$client = container()->get('api.client');
$zakenEndpoint = $client->zaken();
$zaak = $zakenEndpoint->find($zaakUuid);

// The $zaak has references to zaaktype, deelzaken, status, resultaat, etc. At
// this point in time, the $zaak Entity only has an URL reference. However, 
// accessing the attribute causes the plugin to load the actual resource.

$zaaktype = $zaak->zaaktype; // Causes a HTTP request.

// The $zaaktype also has reference to multiple resources. The same thing will
// happen here: accessing the attribute will make the plugin load the full
// resource from the API and return an Entity instance of that resource. 

$statustypen = $zaaktype->statustypen; // Causes a HTTP request.

// In this case, $statustypen is a \OWC\Zaaksysteem\Support\Collection 
// which can be looped over and has additional helper methods.

foreach ($statustypen->sortByAttribute('volgnummer') as $statustype) {
    echo $statustype->omschrijving;
}

// Additional calls to loaded resources will not cause extra HTTP 
// requests. The loaded resource is set on the Entity model.
```

## New API client
There is support for multiple ZGW API clients (OpenZaak, Decos JOIN, etc.). 

To add a new, additional API client, a custom implementation of `OWC\Zaaksysteem\Client\Client` is required. This implementation should update the `AVAILABLE_ENDPOINTS` array to point to the right `Endpoint` implementations.

Additionally a custom `OWC\Zaaksysteem\Http\Authentication\TokenAuthenticator` might be required, as the way of authentication might differ.
