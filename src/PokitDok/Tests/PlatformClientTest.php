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

use PokitDok\Platform\PlatformClient;


class PlatformClientTest extends \PHPUnit_Framework_TestCase
{
    private $eligibility_request = array(
        'member' => array(
            'id' => "W000000000",
            'birth_date' => "1970-01-01",
            'first_name' => "Jane",
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

    private $claim_status_request = array(
        "patient" => array(
            "birth_date" => "1970-01-01",
            "first_name" => "Jane",
            "last_name" => "Doe",
            "id" => "W000000000"
        ),
        "provider" => array(
            "first_name" => "JEROME",
            "last_name" => "AYA-AY",
            "npi" => "1467560003"
        ),
        "service_date" => "2014-01-01",
        "trading_partner_id" => "MOCKPAYER"
    );

    private $referral_request = array(
        "event" => array(
                "category" => "specialty_care_review",
                "certification_type" => "initial",
                "delivery" => array(
                    "quantity" => 1,
                    "quantity_qualifier" => "visits"
                ),
            "diagnoses" => array(
                array(
                    "code" => "384.20",
                    "date" => "2014-09-30"
                )
            ),
            "place_of_service" => "office",
            "provider" => array(
                "first_name" => "JOHN",
                "npi" => "1154387751",
                "last_name" => "FOSTER",
                "phone" => "8645822900"
            ),
            "type" => "consultation",
        ),
        "patient" => array(
            "birth_date" => "1970-01-01",
            "first_name" => "JANE",
            "last_name" => "DOE",
            "id" => "1234567890"
        ),
        "provider" => array(
            "first_name" => "CHRISTINA",
            "last_name" => "BERTOLAMI",
            "npi" => "1619131232"
        ),
        "trading_partner_id" => "MOCKPAYER"
    );

    private $authorization_request = array(
            "event" => array(
                "category" => "health_services_review",
                "certification_type" => "initial",
                "delivery" => array(
                    "quantity" => 1,
                    "quantity_qualifier" => "visits"
                )
            ,
            "diagnoses" => array(
                array(
                    "code" => "789.00",
                    "date" => "2014-10-01"
                )
            ),
            "place_of_service" => "office",
            "provider" => array(
                "organization_name" => "KELLY ULTRASOUND CENTER, LLC",
                "npi" => "1760779011",
                "phone" => "8642341234"
            ),
            "services" => array(
                array(
                    "cpt_code" => "76700",
                    "measurement" => "unit",
                    "quantity" => 1
                )
            ),
            "type" => "diagnostic_imaging"
        ),
        "patient" => array(
            "birth_date" => "1970-01-01",
            "first_name" => "JANE",
            "last_name" => "DOE",
            "id" => "1234567890"
        ),
        "provider" => array(
            "first_name" => "JEROME",
            "npi" => "1467560003",
            "last_name" => "AYA-AY"
        ),
        "trading_partner_id" => "MOCKPAYER"
    );

    private $appointments_request = array(
        "appointment_type" => "SS1",
        "start_date" => "2015-01-14T08:00:00",
        "end_date" => "2015-01-16T17:00:00",
        "patient_uuid" => "8ae236ff-9ccc-44b0-8717-42653cd719d0"
    );

    private $book_appointment_request = array(
        "patient" => array(
            "_uuid" => "500ef469-2767-4901-b705-425e9b6f7f83",
            "email" => "john@johndoe.com",
            "phone" => "800-555-1212",
            "birth_date" => "1970-01-01",
            "first_name" => "John",
            "last_name" => "Doe",
            "member_id" => "M000001"
        ),
        "description" => "Welcome to M0d3rN Healthcare"
    );

    private $update_appointment_request = array("description" => "Welcome to M0d3rN Healthcare");

    const POKITDOK_PLATFORM_API_CLIENT_ID = 'YIxdWJDwSo5uyeNfNniE';
    const POKITDOK_PLATFORM_API_CLIENT_SECRET = 'fLfn4vzp44rZkv9Uun7JuJnFzVImssTJwXX95I3U';
    const POKITDOK_PLATFORM_API_SITE = 'http://127.0.0.1:5002';

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
            PlatformClient::POKITDOK_PLATFORM_API_VERSION_PATH
        );
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
        $this->object->init();
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
        $claim = $this->object->claims($this->claim_request)->body();
        $this->assertObjectHasAttribute('meta', $claim);
        $this->assertObjectHasAttribute('data', $claim);
        $this->assertObjectHasAttribute('claim', $claim->data->parameters);
        $this->assertObjectHasAttribute('subscriber', $claim->data->parameters);
        $this->assertObjectHasAttribute('payer', $claim->data->parameters);
        $this->assertObjectHasAttribute('service_lines', $claim->data->parameters->claim);
        $this->assertObjectHasAttribute('plan_participation', $claim->data->parameters->claim);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::usage
     */
    public function testUsage()
    {
        $usage = $this->object->usage();

        $this->assertObjectHasAttribute('rate_limit_amount', $usage);
        $this->assertObjectHasAttribute('rate_limit_reset', $usage);
        $this->assertObjectHasAttribute('application_mode', $usage);
        $this->assertObjectHasAttribute('processing_time', $usage);
        $this->assertObjectHasAttribute('rate_limit_cap', $usage);
        $this->assertObjectHasAttribute('credits_remaining', $usage);
        $this->assertObjectHasAttribute('credits_billed', $usage);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::providers
     */
    public function testProviders()
    {
        $providers = $this->object->providers(array('state' => 'CA'))->body();

        $this->assertObjectHasAttribute('meta', $providers);
        $this->assertObjectHasAttribute('data', $providers);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::eligibility
     */
    public function testEligibility()
    {
        $eligibility = $this->object->eligibility($this->eligibility_request)->body();

        $this->assertObjectHasAttribute('meta', $eligibility);
        $this->assertObjectHasAttribute('data', $eligibility);
        $this->assertObjectHasAttribute('provider', $eligibility->data);
        $this->assertObjectHasAttribute('subscriber', $eligibility->data);
        $this->assertObjectHasAttribute('payer', $eligibility->data);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::claimsStatus
     * @todo   Implement testClaimsStatus().
     */
    public function testClaimsStatus()
    {
        $claims_status = $this->object->claimsStatus($this->claim_status_request)->body();

        $this->assertObjectHasAttribute('meta', $claims_status);
        $this->assertObjectHasAttribute('data', $claims_status);
        $this->assertObjectHasAttribute('patient', $claims_status->data);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::enrollment
     * @todo   Implement testEnrollment().
     */
    public function testEnrollment()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::pricesInsurance
     */
    public function testPricesInsurance()
    {
        $prices_insurance = $this->object->pricesInsurance(array('cpt_code' => "87799", 'zip_code' => "32218"))->body();

        $this->assertObjectHasAttribute('meta', $prices_insurance);
        $this->assertObjectHasAttribute('data', $prices_insurance);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::pricesCash
     */
    public function testPriceCash()
    {
        $prices_cash = $this->object->pricesCash(array('cpt_code' => "87799", 'zip_code' => "32218"))->body();

        $this->assertObjectHasAttribute('meta', $prices_cash);
        $this->assertObjectHasAttribute('data', $prices_cash);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::activities
     */
    public function testActivities()
    {
        $activities = $this->object->activities()->body();

        $this->assertObjectHasAttribute('meta', $activities);
        $this->assertObjectHasAttribute('data', $activities);
        $this->assertObjectHasAttribute('units_of_work', $activities->data[0]);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::tradingpartners with no id
     */
    public function testTradingPartnersIndex()
    {
        $trading_partners = $this->object->trading_partners()->body();

        $this->assertObjectHasAttribute('meta', $trading_partners);
        $this->assertObjectHasAttribute('data', $trading_partners);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::tradingpartners with MOCKPAYER
     */
    public function testTradingPartnersGet()
    {
        $trading_partners = $this->object->trading_partners('MOCKPAYER')->body();

        $this->assertObjectHasAttribute('meta', $trading_partners);
        $this->assertObjectHasAttribute('data', $trading_partners);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::plans with no arguments
     */
    public function testPlansNoArguments()
    {
        $plans_no_arg = $this->object->plans()->body();

        $this->assertObjectHasAttribute('meta', $plans_no_arg);
        $this->assertObjectHasAttribute('data', $plans_no_arg);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::plans with arguments
     */
    public function testPlans()
    {
        $plans = $this->object->plans(array('state' => "TX", 'plan_type' => "PPO"))->body();

        $this->assertObjectHasAttribute('meta', $plans);
        $this->assertObjectHasAttribute('data', $plans);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::referrals
     */
    public function testReferrals()
    {
        $referrals = $this->object->referrals($this->referral_request)->body();

        $this->assertObjectHasAttribute('meta', $referrals);
        $this->assertObjectHasAttribute('data', $referrals);

        $this->assertSame($referrals->data->event->review->certification_number, 'AUTH0001');
        $this->assertSame($referrals->data->event->review->certification_action, 'certified_in_total');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::authorizations
     */
    public function testAuthorizations()
    {
        $authorizations = $this->object->authorizations($this->authorization_request)->body();

        $this->assertObjectHasAttribute('meta', $authorizations);
        $this->assertObjectHasAttribute('data', $authorizations);

        $this->assertSame($authorizations->data->event->review->certification_number, 'AUTH0001');
        $this->assertSame($authorizations->data->event->review->certification_action, 'certified_in_total');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::schedulers
     */
    public function testSchedulers()
    {
        $schedulers = $this->object->schedulers()->body();

        $this->assertObjectHasAttribute('meta', $schedulers);
        $this->assertObjectHasAttribute('data', $schedulers);

        $this->assertSame($schedulers->data[0]->name, 'Greenway');
        $this->assertSame($schedulers->data[1]->name, 'Athena');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::schedulers
     */
    public function testSchedulersUUID()
    {
        $schedulers = $this->object->schedulers('d8f38f08-8530-11e4-9a71-0800272e8da1')->body();

        $this->assertObjectHasAttribute('meta', $schedulers);
        $this->assertObjectHasAttribute('data', $schedulers);

        $this->assertSame($schedulers->data[0]->scheduler_uuid, 'd8f38f08-8530-11e4-9a71-0800272e8da1');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointment_types
     */
    public function testAppointmentTypes()
    {
        $appointment_types = $this->object->appointment_types()->body();

        $this->assertObjectHasAttribute('meta', $appointment_types);
        $this->assertObjectHasAttribute('data', $appointment_types);

        $this->assertSame($appointment_types->data[0]->appointment_type_uuid, 'ef987691-0a19-447f-814d-f8f3abbf4860');
        $this->assertSame($appointment_types->data[1]->appointment_type_uuid, 'ef987692-0a19-447f-814d-f8f3abbf4860');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointment_types
     */
    public function testAppointmentTypesUUID()
    {
        $appointment_types = $this->object->appointment_types('ef987691-0a19-447f-814d-f8f3abbf4860')->body();

        $this->assertObjectHasAttribute('meta', $appointment_types);
        $this->assertObjectHasAttribute('data', $appointment_types);

        $this->assertSame($appointment_types->data[0]->appointment_type_uuid, 'ef987691-0a19-447f-814d-f8f3abbf4860');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointment_types
     */
    public function testAppointments()
    {
        $appointments = $this->object->appointments($this->appointments_request)->body();

        $this->assertObjectHasAttribute('meta', $appointments);
        $this->assertObjectHasAttribute('data', $appointments);

        $this->assertSame($appointments->data[0]->appointment_type, 'SS1');
        $this->assertSame($appointments->data[0]->start_date, '2015-01-14T08:00:00.000000');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointment_types
     */
    public function testAppointmentsUUID()
    {
        $appointments = $this->object->appointments('ef987691-0a19-447f-814d-f8f3abbf4859')->body();

        $this->assertObjectHasAttribute('meta', $appointments);
        $this->assertObjectHasAttribute('data', $appointments);

        $this->assertSame($appointments->data[0]->pd_appointment_uuid, 'ef987691-0a19-447f-814d-f8f3abbf4859');
        $this->assertSame($appointments->data[0]->appointment_type, 'OV1');
        $this->assertSame($appointments->data[0]->patient->email, 'john@johndoe.com');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::book_appointment
     */
    public function testBookAppointment()
    {
        $appointment = $this->object->book_appointment(
            'ef987691-0a19-447f-814d-f8f3abbf4859',
            $this->book_appointment_request
        )->body();

        $this->assertObjectHasAttribute('meta', $appointment);
        $this->assertObjectHasAttribute('data', $appointment);

        $this->assertSame($appointment->data->appointment_type, 'OV1');
        $this->assertSame($appointment->data->booked, true);
        $this->assertSame($appointment->data->patient->email, 'john@johndoe.com');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::update_appointment
     */
    public function testUpdateAppointment()
    {
        $appointment = $this->object->update_appointment(
            'ef987691-0a19-447f-814d-f8f3abbf4859',
            $this->update_appointment_request
        )->body();

        $this->assertObjectHasAttribute('meta', $appointment);
        $this->assertObjectHasAttribute('data', $appointment);

        $this->assertSame($appointment->data->appointment_type, 'OV1');
        $this->assertSame($appointment->data->booked, false);
        $this->assertSame($appointment->data->description, "Welcome to M0d3rN Healthcare");
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::cancel_appointment
     */
    public function testCancelAppointment()
    {
        $this->object->cancel_appointment('ef987691-0a19-447f-814d-f8f3abbf4859');

        $this->assertSame($this->object->getStatus(), 204);
    }
}
