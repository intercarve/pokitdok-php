<?php
// Copyright (C) 2014, All Rights Reserved, PokitDok, Inc.
// http://www.pokitdok.com
//
// Please see the LICENSE.txt file for more information.
// All other rights reserved.
//
//	PokitDok Platform Client for PHP Example
//		Consume the REST based PokitDok platform API
//		https://platform.pokitdok.com/login#/documentation

require_once 'vendor/autoload.php';
// If not using composer remove previous line and uncomment following two lines
//require_once 'src/PokitDok/Common/Oauth2ApplicationClient.php';
//require_once 'src/PokitDok/Platform/PlatformClient.php';

use PokitDok\Platform\PlatformClient;

// Change to your PokitDok Platform API Client ID and Secret:
//      Go to https://platform.pokitdok.com to get your API key
//define("POKITDOK_PLATFORM_API_CLIENT_ID", 'your client id');
//define("POKITDOK_PLATFORM_API_CLIENT_SECRET", 'your client secret');
define("POKITDOK_PLATFORM_API_CLIENT_ID", 'JcR2P8SmoIaon4vpN9Q9');
define("POKITDOK_PLATFORM_API_CLIENT_SECRET", 'JqPijdEL2NYFTJLEKquUzMgAks6JmWyszrbRPk4X');




try {

    $client = new PlatformClient(POKITDOK_PLATFORM_API_CLIENT_ID, POKITDOK_PLATFORM_API_CLIENT_SECRET);

// For internal testing only
    define("POKITDOK_PLATFORM_API_SITE", 'http://me.pokitdok.com:5002');
    $client->setApiBaseUrl(
        POKITDOK_PLATFORM_API_SITE .
        PlatformClient::POKITDOK_PLATFORM_API_VERSION_PATH);
    $client->setApiTokenUrl(
        POKITDOK_PLATFORM_API_SITE .
        PlatformClient::POKITDOK_PLATFORM_API_TOKEN_URL
    );
// end internal testing only

    echo "Usage: ". print_r($client->usage(), true) . PHP_EOL;

    $providers_response = $client->providers(array('state' => 'CA'));
    echo "Providers Response Headers: ".
        print_r($providers_response->header(), true) .
        PHP_EOL;
    echo "Providers Response Body: ".
        print_r($providers_response->body(), true) .
        PHP_EOL;

    echo "Payers: ". print_r($client->payers()->body()) . PHP_EOL;

    echo "Eligibility Response: ".
        print_r(
            $client->eligibility(
                array(
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
                )
            )->body(),
            true
        ) .
        PHP_EOL;

    echo "File upload: ".
        print_r(
            $client->files("./src/PokitDok/Tests/general-physician-office-visit.270", "MOCKPAYER")->body()
        ) .
        PHP_EOL;

    $claim_response = $client->claims(
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
    echo "Claim Response: ". print_r($claim_response->body(), true) . PHP_EOL;

} catch (\Exception $e) {
    echo "Exception (". $e->getCode() ."): ". $e->getMessage();
}
