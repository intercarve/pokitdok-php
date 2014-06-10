<?php
// Copyright (C) 2014, All Rights Reserved, PokitDok, Inc.
// http://www.pokitdok.com
//
// Please see the LICENSE.txt file for more information.
// All other rights reserved.
//
//	PokitDok Platform Client for PHP Tests
//		Consume the REST based PokitDok platform API
//		https://platform.pokitdok.com/login#/documentation


namespace PokitDok\Tests;

require_once 'vendor/autoload.php';
// If not using composer remove previous line and uncomment following two lines
//require_once 'src/PokitDok/Common/Oauth2ApplicationClient.php';
//require_once 'src/PokitDok/Platform/PlatformClient.php';

use VCR\VCR as VCR;
VCR::configure()->setCassettePath(__DIR__ . "/vcr_cassettes");
VCR::turnOn();

use PokitDok\Platform\PlatformClient;


class PlatformClientTest extends \PHPUnit_Framework_TestCase
{
    private $eligibility_request = array(
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
    );

    private $claim_request = array(
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
    );


    const POKITDOK_PLATFORM_API_CLIENT_ID = 'JcR2P8SmoIaon4vpN9Q9';
    const POKITDOK_PLATFORM_API_CLIENT_SECRET = 'JqPijdEL2NYFTJLEKquUzMgAks6JmWyszrbRPk4X';
    const POKITDOK_PLATFORM_API_SITE = 'http://me.pokitdok.com:5002';

    /**
     * @var PlatformClient
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PlatformClient(
            self::POKITDOK_PLATFORM_API_CLIENT_ID,
            self::POKITDOK_PLATFORM_API_CLIENT_SECRET
        );

        $this->object->setApiBaseUrl(
            self::POKITDOK_PLATFORM_API_SITE .
            PlatformClient::POKITDOK_PLATFORM_API_VERSION_PATH);
        $this->object->setApiTokenUrl(
            self::POKITDOK_PLATFORM_API_SITE .
            PlatformClient::POKITDOK_PLATFORM_API_TOKEN_URL
        );

        $this->authenticate();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object = null;
    }

    protected function authenticate()
    {
        VCR::insertCassette("authenticate.yml");
        $this->object->init();
        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::setVersionPath
     */
    public function testSetVersionPath()
    {
        $version_path = "/api/v5";

        $this->object->setVersionPath($version_path);
        $this->assertEquals($this->object->getVersionPath(), $version_path);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::getVersionPath
     */
    public function testGetVersionPath()
    {
        $this->assertEquals($this->object->getVersionPath(), PlatformClient::POKITDOK_PLATFORM_API_VERSION_PATH);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::claims
     * @todo   Implement testClaims().
     */
    public function testClaims()
    {
        VCR::insertCassette("claims.yml");

        $claim = $this->object->claims($this->claim_request)->body();
        $this->assertObjectHasAttribute('meta', $claim);
        $this->assertObjectHasAttribute('data', $claim);
        $this->assertObjectHasAttribute('claim', $claim->data->parameters);
        $this->assertObjectHasAttribute('subscriber', $claim->data->parameters);
        $this->assertObjectHasAttribute('payer', $claim->data->parameters);
        $this->assertObjectHasAttribute('service_lines', $claim->data->parameters->claim);
        $this->assertObjectHasAttribute('plan_participation', $claim->data->parameters->claim);

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::usage
     */
    public function testUsage()
    {
        VCR::insertCassette("usage.yml");

        $usage = $this->object->usage();

        $this->assertObjectHasAttribute('rate_limit_amount', $usage);
        $this->assertObjectHasAttribute('rate_limit_reset', $usage);
        $this->assertObjectHasAttribute('test_mode', $usage);
        $this->assertObjectHasAttribute('processing_time', $usage);
        $this->assertObjectHasAttribute('rate_limit_cap', $usage);
        $this->assertObjectHasAttribute('credits_remaining', $usage);
        $this->assertObjectHasAttribute('credits_billed', $usage);

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::providers
     */
    public function testProviders()
    {
        VCR::insertCassette("providers.yml");

        $providers = $this->object->providers(array('state' => 'CA'))->body();

        $this->assertObjectHasAttribute('meta', $providers);
        $this->assertObjectHasAttribute('data', $providers);

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::eligibility
     */
    public function testEligibility()
    {
        VCR::insertCassette("eligibility.yml");

        $eligibility = $this->object->eligibility($this->eligibility_request)->body();

        $this->assertObjectHasAttribute('meta', $eligibility);
        $this->assertObjectHasAttribute('data', $eligibility);
        $this->assertObjectHasAttribute('provider', $eligibility->data);
        $this->assertObjectHasAttribute('subscriber', $eligibility->data);
        $this->assertObjectHasAttribute('payer', $eligibility->data);

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::claimsStatus
     * @todo   Implement testClaimsStatus().
     */
    public function testClaimsStatus()
    {
        VCR::insertCassette("claims_status.yml");

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::enrollment
     * @todo   Implement testEnrollment().
     */
    public function testEnrollment()
    {
        VCR::insertCassette("enrollment.yml");

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::payers
     */
    public function testPayers()
    {
        VCR::insertCassette("payers.yml");

        $payers = $this->object->payers(array('state' => 'CA'))->body();

        $this->assertObjectHasAttribute('meta', $payers);
        $this->assertObjectHasAttribute('data', $payers);
        $this->assertObjectHasAttribute('supported_transactions', $payers->data[0]);

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::priceInsurance
     * @todo   Implement testPriceInsurance().
     */
    public function testPriceInsurance()
    {
        VCR::insertCassette("price_insurance.yml");

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::priceCash
     * @todo   Implement testPriceCash().
     */
    public function testPriceCash()
    {
        VCR::insertCassette("price_cash.yml");

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        VCR::eject();
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::activities
     */
    public function testActivities()
    {
        VCR::insertCassette("activities.yml");

        $activities = $this->object->activities()->body();

        $this->assertObjectHasAttribute('meta', $activities);
        $this->assertObjectHasAttribute('data', $activities);
        $this->assertObjectHasAttribute('units_of_work', $activities->data[0]);

        VCR::eject();
    }
}
