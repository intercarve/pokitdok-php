[![Build Status](https://travis-ci.org/pokitdok/pokitdok-php.svg?branch=master)](https://travis-ci.org/pokitdok/pokitdok-php)

pokitdok-php
=============

PokitDok [Platform API][apidocs] Client for PHP

## Installation
Simply add a dependency on pokitdok/pokitdok-php to your project's composer.json file if you use Composer to manage the dependencies of your project. Here is a minimal example of a composer.json:

```json
    {
        "require": {
            "pokitdok/pokitdok-php": "dev-master"
        }
    }
```

## Tests
```
    phpunit src/PokitDok/Tests/
```

## Resources
* [Read the PokitDok API docs][apidocs]
* [View Source on GitHub][code]
* [Report Issues on GitHub][issues]

[apidocs]: https://platform.pokitdok.com/dashboard#/documentation
[code]: https://github.com/PokitDok/pokitdok-php
[issues]: https://github.com/PokitDok/pokitdok-php/issues

## Quick Start

```php
    # initialize environment
    composer init --require="pokitdok/pokitdok-php:dev-master"
    composer install

    # initialize the client
    include 'vendor/autoload.php';

    $client = new PokitDok\Platform\PlatformClient('your_client_id', 'your_client_secret');

    #retrieve provider information by NPI
    $client->providers('1467560003');

    #search providers by name (individuals)
    $client->providers(array('first_name' => "Jerome", 'last_name' => "Aya-Ay"));

    #search providers by name (organizations)
    $client->providers(array('organization_name' => "Qliance"));

    #search providers by location and/or specialty
    $client->providers(array('zipcode' => "90210", 'radius' => "10mi"));
    $client->providers(array('zipcode' => "90210", 'radius' => "10mi", 'specialty' => "RHEUMATOLOGY"));

    #submit a v4 eligibility request
    $client->eligibility(array(
           'member' => array(
               'id' => "W000000000",
               'birth_date' => "1970-01-01",
               'last_name' => "Doe"
           ),
           'provider' => array(
               'npi' => "1467560003",
               'last_name' => "AYA-AY",
               'first_name' => "JEROME"
           ),
           'service_types' => array("health_benefit_plan_coverage"),
           'trading_partner_id' => "MOCKPAYER"
    ));

    #submit a v4 claims request
    $client->claims(
        array(
            'transaction_code' => "chargeable",
            'trading_partner_id' => "MOCKPAYER",
            'billing_provider' => array(
                'taxonomy_code' => "207Q00000X",
                'first_name' => "Jerome",
                'last_name' => "Aya-Ay",
                'npi' => "1467560003",
                'address' => array(
                    'address_lines' => array(
                        "8311 WARREN H ABERNATHY HWY"
                    ),
                    'city' => "SPARTANBURG",
                    'state' => "SC",
                    'zipcode' => "29301"
                ),
                'tax_id' => "123456789"
            ),
            'subscriber' => array(
                'first_name' => "Jane",
                'last_name' => "Doe",
                'member_id' => "W000000000",
                'address' => array(
                    'address_lines' => array("123 N MAIN ST"),
                    'city' => "SPARTANBURG",
                    'state' => "SC",
                    'zipcode' => "29301"
                ),
                'birth_date' => "1970-01-01",
                'gender' => "female"
            ),
            'claim' => array(
                'total_charge_amount' => 60.0,
                'service_lines' => array(
                    array(
                        'procedure_code' => "99213",
                        'charge_amount' => 60.0,
                        'unit_count' => 1.0,
                        'diagnosis_codes' => array(
                            "487.1"
                        ),
                        'service_date' => "2014-06-01"
                    )
                )
            ),
            "payer" =>
                array(
                    'organization_name' => "Acme Ins Co",
                    'plan_id' => "1234567890"
                )
    ));

    #Submit X12 files directly for processing on the platform
    $client->files("./src/PokitDok/Tests/general-physician-office-visit.270", "MOCKPAYER");

    #Check on pending platform activities

    #check on a specific activity
    $client->activities('5362b5a064da150ef6f2526c');

    #check on a batch of activities
    $client->activities(array('parent_id' => "537cd4b240b35755f5128d5c"));

    #retrieve an index of activities
    $client->activities();
```

## Tested PHP Versions
This library aims to support and is tested against these PHP versions:

* php >= 5.3

You may have luck with other interpreters - let us know how it goes.

## License
Copyright (c) 2014 PokitDok Inc. See [LICENSE][] for details.

[license]: LICENSE.txt


