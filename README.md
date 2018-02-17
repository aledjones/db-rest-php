db-rest-php
-----------

A library for working with data provided by [derhuerst/db-rest](https://github.com/derhuerst/db-rest).
By default the most recent hosted endpoint is used, but the ``Client()`` function can be initialized with your own base
URL.  

The main purpose of this project is to translate API responses directly into PHP objects. This makes working with the
API easier and more convenient. _(at least for me)_

Keep in mind that this is a private project created mainly for internal use and therefore can be subject to sudden
changes in structure and/or functionality.

## Installation
The easiest way to install this package is via [Composer](http://getcomposer.org/) in your ``composer.json`` file.

```json
{
  "require": {
      "aledjones/db-rest-php": "*"
    }
}
```
Use `composer install` to install needed dependencies.

If you are unable to use composer in your project, download the files directly and ``require`` the files you need. Make
sure to grab a copy of [httpful](http://phphttpclient.com/) as well.  

## Example

```php
$client = new db_rest_php\Client();

return $client->GetStationsByQuery("Berlin");
// Returns array of station objects matching the query pattern
// Results provided by db-rest from db-stations-autocomplete

return $client->GetStationDetailsById('8002549')->address->zipcode;
// Returns "zipcode" attribute of the address object, an attribute of the StationDetails object
// Output will be 20099

return $client->GetStationByName("Kaiserslautern West");
// Returns StationDetails object
// Same as GetStationDetailsById(), but input is the full name of the station as string.
// This needs to be the exact name or otherwise the API will show a strange behavior (illegal json).
// Try to feed it with the "name" string from a GetStationsByQuery() result.
```

Each ``station`` object provides all possible attributes, some of them are objects as well (eg. `address`or
`regionalbereich`). All possible attributes as documented with their designated data type.  
If an attribute is empty, the attribute does not apply to the requested station.  

---
I'm am always open for suggestions. If you find errors or know how to tune a thing or two, please feel free to open an
issue on GitHub.  This is a pre-release/dev-release.

This library will grow over time in functionality. Make sure you check back for updates every now and then.