# TaxJar SDK

This library provides integration support with the [TaxJar API](http://www.taxjar.com/api/docs/).


## Prerequisites

To use the TaxJar SDK, you must obtain a [TaxJar API token](http://www.taxjar.com/api_sign_up).

## Getting Started

We suggest you use Composer to install the TaxJar SDK, well, because it's simple. Just do this:

Create or add to your existing `composer.json` file:

```json
{
    "require": {
        "okanjo/taxjar": "*"
    }
}
```

Then do the install or update by:

```shell
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

And use the Composer autoloader to include everything like magic:

```php
<?php
require_once 'vendor/autoload.php';

$api = new TaxJar\Api\Client('YOUR_API_KEY');

// ...
```

## Smart Sales Tax


```php

$res = $api->getSalesTax()->where(
    array(
        \TaxJar\Api\Common\Fields::AMOUNT => "10.00",
        \TaxJar\Api\Common\Fields::SHIPPING => "2.00",

        \TaxJar\Api\Common\Fields::FROM_CITY => "Ramsey",
        \TaxJar\Api\Common\Fields::FROM_STATE => "NJ",
        \TaxJar\Api\Common\Fields::FROM_ZIP => "07446",
        \TaxJar\Api\Common\Fields::FROM_COUNTRY => "US",

        \TaxJar\Api\Common\Fields::TO_CITY => "Freehold",
        \TaxJar\Api\Common\Fields::TO_STATE => "NJ",
        \TaxJar\Api\Common\Fields::TO_ZIP => "07728",
        \TaxJar\Api\Common\Fields::TO_COUNTRY => "US",
    )
)->execute();

// Note: You can switch from associative de-serialization (default)
//       to objects (do execute(false) instead)

if ($res->status == \TaxJar\Api\Response::HTTP_OK) {
    $amountToCollect = $res->data['amount_to_collect'];
} else {
    // API response was wonky
}

print_r($res);

```

Results:

```
TaxJar\Api\Response Object
(
    [status] => 200
    [data] => Array
        (
            [amount_to_collect] => 0.84
            [rate] => 0.07
            [has_nexus] => 1
            [freight_taxable] => 1
            [tax_source] => destination
        )

    [headers] => Array
        (
            [SERVER] => Cowboy
            [CONNECTION] => close
            [DATE] => Sat, 22 Nov 2014 06:55:12 GMT
            [STATUS] => 200 OK
            [X-FRAME-OPTIONS] => SAMEORIGIN
            [X-XSS-PROTECTION] => 1; mode=block
            [X-CONTENT-TYPE-OPTIONS] => nosniff
            [X-UA-COMPATIBLE] => chrome=1
            [CONTENT-TYPE] => application/json; charset=utf-8
            [ETAG] => "a9ea7272ca4facfde21f8e196a21790a"
            [CACHE-CONTROL] => max-age=0, private, must-revalidate
            [X-REQUEST-ID] => ae167d02-9dbf-46c4-86db-ed77f0b7414e
            [X-RUNTIME] => 0.035393
            [VIA] => 1.1 vegur
        )

    [contentType] => application/json; charset=utf-8
    [raw] => {"amount_to_collect":0.84,"rate":0.07,"has_nexus":true,"freight_taxable":true,"tax_source":"destination"}
)
```

## Location Tax Rate Lookup

```php

// By zip
$res = $api->getTaxRate('53132')->execute();

// By city and zip
$res = $api->getTaxRate('53132', 'Franklin')->execute();

// By city, state and zip
$res = $api->getTaxRate('53132', 'Franklin', 'US')->execute();

// Also Canada.
// By city, state and zip
$res = $api->getTaxRate("N8W1Y3", "ON", "CA")->execute();

if ($res->status == \TaxJar\Api\Response::HTTP_OK) {
    $taxRate = $res->data['location']['combined_rate'];
} else {
    // API response was wonky
}

print_r($res);

```

Results:

```
TaxJar\Api\Response Object
(
    [status] => 200
    [data] => Array
        (
            [location] => Array
                (
                    [zip] => N8W1Y3
                    [city] => Windsor
                    [state] => ON
                    [country] => CA
                    [combined_rate] => 0.13
                )

        )

    [headers] => Array
        (
            [SERVER] => Cowboy
            [CONNECTION] => close
            [DATE] => Sat, 22 Nov 2014 06:11:49 GMT
            [STATUS] => 200 OK
            [X-FRAME-OPTIONS] => SAMEORIGIN
            [X-XSS-PROTECTION] => 1; mode=block
            [X-CONTENT-TYPE-OPTIONS] => nosniff
            [X-UA-COMPATIBLE] => chrome=1
            [CONTENT-TYPE] => application/json; charset=utf-8
            [ETAG] => "294c32f11b6aed31f8d54326be8ccc47"
            [CACHE-CONTROL] => max-age=0, private, must-revalidate
            [X-REQUEST-ID] => b216cc4e-931c-489f-b7b7-e87dd00b7afc
            [X-RUNTIME] => 0.072457
            [VIA] => 1.1 vegur
        )

    [contentType] => application/json; charset=utf-8
    [raw] => {"location":{"zip":"N8W1Y3","city":"Windsor","state":"ON","country":"CA","combined_rate":"0.13"}}
)
```