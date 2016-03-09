<?php
/**
 * Copyright (C) 2014, All Rights Reserved, PokitDok, Inc.
 * http://www.pokitdok.com
 *
 * Please see the LICENSE.txt file for more information.
 * All other rights reserved.
 *
 *	PokitDok Platform Client for PHP
 *		Consume the REST based PokitDok platform API
 *		https://platform.pokitdok.com/
 */


namespace PokitDok\Tests;

/**
 Following line is when used with Composer
 */
require_once 'vendor/autoload.php';
/** If not using Composer remove previous line and uncomment following two lines
require_once 'src/PokitDok/Common/Oauth2ApplicationClient.php';
require_once 'src/PokitDok/Platform/PlatformClient.php';
*/

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
    
    private $enrollment_request = array(
        "action" => "Change",
        "dependents" => array(),
        "master_policy_number" => "ABCD012354",
        "payer" => array(
            "tax_id" => "654456654"
        ),
        "purpose" => "Original",
        "sponsor" => array(
        "tax_id" => "999888777"
        ),
        "subscriber" => array(
        "address" => array(
            "city" => "CAMP HILL",
                "county" => "CUMBERLAND",
                "line" => "100 MARKET ST",
                "line2" => "APT 3G",
                "postal_code" => "17011",
                "state" => "PA"
            ),
            "benefit_status" => "Active",
            "benefits" => array(
                array(
                    "begin_date" => "2015-01-01",
                    "benefit_type" => "Health",
                    "coordination_of_benefits" => array(
                        array(
                            "group_or_policy_number" => "890111",
                            "payer_responsibility" => "Primary",
                            "status" => "Unknown"
                        )
                    ),
                    "late_enrollment" => False,
                    "maintenance_type" => "Addition"
                ),
                array(
                    "begin_date" => "2015-01-01",
                    "benefit_type" => "Dental",
                    "late_enrollment" => False,
                    "maintenance_type" => "Addition"
                ),
                array(
                    "begin_date" => "2015-01-01",
                    "benefit_type" => "Vision",
                    "late_enrollment" => False,
                    "maintenance_type" => "Addition"
                )
            ),
            "birth_date" => "1940-01-01",
            "contacts" => array(
                array(
                    "communication_number2" => "7172341240",
                    "communication_type2" => "Work Phone Number",
                    "primary_communication_number" => "7172343334",
                    "primary_communication_type" => "Home Phone Number"
                )
            ),
            "eligibility_begin_date" => "2014-01-01",
            "employment_status" => "Full-time",
            "first_name" => "JOHN",
            "gender" => "Male",
            "group_or_policy_number" => "123456001",
            "handicapped" => False,
            "last_name" => "DOE",
            "maintenance_reason" => "Active",
            "maintenance_type" => "Addition",
            "member_id" => "123456789",
            "middle_name" => "P",
            "relationship" => "Self",
            "ssn" => "123456789",
            "subscriber_number" => "123456789",
            "substance_abuse" => False,
            "tobacco_use" => False
        ),
        "trading_partner_id" => "MOCKPAYER",
    );

    private $enrollment_snapshot_request_file = "./src/PokitDok/Tests/enrollment.834";

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
            "uuid" => "500ef469-2767-4901-b705-425e9b6f7f83",
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

    private $ccd_request = array(
        "trading_partner_id" => "MOCKPAYER",
        "patient" => array(
            'first_name' => "Jane",
            'last_name' => "Doe",
            'address' => array(
                'address_lines' => array("123 N MAIN ST"),
                'city' => "SPARTANBURG",
                'state' => "SC",
                'zipcode' => "29301"
            ),
            'birth_date' => "1970-01-01",
            'gender' => "female"
        ),
        "service_date" => "2016-03-02",
        "rendering_provider" => array(
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
        ));

    private $claims_convert_request_file = "./src/PokitDok/Tests/claims_convert.837";

    private $identity_request = array(
        "prefix" => "Mr.",
        "first_name" => "Oscar",
        "middle_name" => "Harold",
        "last_name" => "Whitmire",
        "suffix" => "IV",
        "birth_date" => "2000-05-01",
        "gender" => "male",
        "email" => "oscar@pokitdok.com",
        "phone" => "555-555-5555",
        "secondary_phone" => "333-333-4444",
        "address" => array(
            "address_lines" => array("1400 Anyhoo Avenue"),
            "city" => "Springfield",
            "state" => "IL",
            "zipcode" => "90210"
        ),
        "identifiers" => array(
            array(
                "location" => array(-121.93831, 37.53901),
                "provider_uuid" => "1917f12b-fb6a-4016-93bc-adeb83204c83",
                "system_uuid" => "967d207f-b024-41cc-8cac-89575a1f6fef",
                "value" => "W90100-IG-88"
            )
        )
    );

    const POKITDOK_PLATFORM_API_CLIENT_ID = '4pET1V8nqYwkR9ge528U';
    const POKITDOK_PLATFORM_API_CLIENT_SECRET = 'l3dp3yJtvKgCbeQEH4NLar3xXQpOuq4wUAvZZgii';
    const POKITDOK_PLATFORM_API_SITE = 'http://platform.pokitdok.com';

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
     * @covers PokitDok\Platform\PlatformClient::claims
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
     * @covers PokitDok\Platform\PlatformClient::claims_status
     */
    public function testClaimsStatus()
    {
        $claims_status = $this->object->claims_status($this->claim_status_request)->body();

        $this->assertObjectHasAttribute('meta', $claims_status);
        $this->assertObjectHasAttribute('data', $claims_status);
        $this->assertObjectHasAttribute('patient', $claims_status->data);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::enrollment
     */
    public function testEnrollment()
    {
        $enrollment_response = $this->object->enrollment($this->enrollment_request)->body();

        $this->assertObjectHasAttribute('meta', $enrollment_response);
        $this->assertObjectHasAttribute('data', $enrollment_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::get_enrollment_snapshot
     */
    public function testGetEnrollmentSnapshot()
    {
        $enrollment_response = $this->object->get_enrollment_snapshot("7e7c54e1-4edf-40ba-aa36-01e523b42e28")->body();

        $this->assertObjectHasAttribute('meta', $enrollment_response);
        $this->assertObjectHasAttribute('data', $enrollment_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::get_enrollment_snapshot_data
     */
    public function testGetEnrollmentSnapshotData()
    {
        $this->markTestIncomplete('This test has not been implemented (server side) yet.');

        //$enrollment_response = $this->object->get_enrollment_snapshot_data(
        //    "7e7c54e1-4edf-40ba-aa36-01e523b42e28")->body();

        //$this->assertObjectHasAttribute('meta', $enrollment_response);
        //$this->assertObjectHasAttribute('data', $enrollment_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::enrollment_snapshot
     */
    public function testEnrollmentSnapshot()
    {
        $enrollment_response = $this->object->enrollment_snapshot(
            $this->enrollment_snapshot_request_file,
            "MOCKPAYER")->body();

        $this->assertObjectHasAttribute('meta', $enrollment_response);
        $this->assertObjectHasAttribute('data', $enrollment_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::insurance_prices
     */
    public function testPricesInsurance()
    {
        $prices_insurance = $this->object->insurance_prices(
            array('cpt_code' => "87799", 'zip_code' => "32218"))->body();

        $this->assertObjectHasAttribute('meta', $prices_insurance);
        $this->assertObjectHasAttribute('data', $prices_insurance);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::cash_prices
     */
    public function testPriceCash()
    {
        $prices_cash = $this->object->cash_prices(array('cpt_code' => "87799", 'zip_code' => "32218"))->body();

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
        $this->assertSame($schedulers->data[0]->name, 'PokitDok');
        $this->assertSame($schedulers->data[1]->name, 'Greenway');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::schedulers
     */
    public function testSchedulersUUID()
    {
        $schedulers = $this->object->schedulers('967d207f-b024-41cc-8cac-89575a1f6fef')->body();

        $this->assertObjectHasAttribute('meta', $schedulers);
        $this->assertObjectHasAttribute('data', $schedulers);
        $this->assertSame($schedulers->data[0]->scheduler_uuid, '967d207f-b024-41cc-8cac-89575a1f6fef');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointment_types
     */
    public function testAppointmentTypes()
    {
        $appointment_types = $this->object->appointment_types()->body();

        $this->assertObjectHasAttribute('meta', $appointment_types);
        $this->assertObjectHasAttribute('data', $appointment_types);
        $this->assertSame($appointment_types->data[0]->appointment_type_uuid, 'a3a45130-4adb-4d2c-9411-85a9d9ac4aa2');
        $this->assertSame($appointment_types->data[1]->appointment_type_uuid, '3ee5c84a-b878-4ce2-b8cc-77743e2f9be1');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointment_types
     */
    public function testAppointmentTypesUUID()
    {
        $appointment_types = $this->object->appointment_types('a3a45130-4adb-4d2c-9411-85a9d9ac4aa2')->body();

        $this->assertObjectHasAttribute('meta', $appointment_types);
        $this->assertObjectHasAttribute('data', $appointment_types);
        $this->assertSame($appointment_types->data[0]->appointment_type_uuid, 'a3a45130-4adb-4d2c-9411-85a9d9ac4aa2');
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::appointments
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
     * @covers PokitDok\Platform\PlatformClient::appointments
     */
    public function testAppointmentsUUID()
    {
        $appointments = $this->object->appointments('bf8440b1-fd20-4994-bb28-e3981833e796')->body();

        $this->assertObjectHasAttribute('meta', $appointments);
        $this->assertObjectHasAttribute('data', $appointments);
        $this->assertSame($appointments->data[0]->pd_appointment_uuid, 'bf8440b1-fd20-4994-bb28-e3981833e796');
        $this->assertSame($appointments->data[0]->appointment_type, 'OV1');
        $this->assertSame($appointments->data[0]->patient->email, 'peg@emailprovider.com');
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

        $this->assertSame($this->object->getStatus(), 200);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::medical_procedure_code
     */
    public function testMedicalProcedureCode()
    {
        $medical_procedure_codes = $this->object->medical_procedure_code()->body();

        $this->assertObjectHasAttribute('meta', $medical_procedure_codes);
        $this->assertObjectHasAttribute('data', $medical_procedure_codes);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::ccd
     */
    public function testCCD()
    {
        $ccd_response = $this->object->ccd($this->ccd_request)->body();

        $this->assertObjectHasAttribute('meta', $ccd_response);
        $this->assertObjectHasAttribute('data', $ccd_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::icd_convert
     */
    public function testIcdConvert()
    {
        $icd_response = $this->object->icd_convert("250.12")->body();

        $this->assertObjectHasAttribute('meta', $icd_response);
        $this->assertObjectHasAttribute('data', $icd_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::claims_convert
     */
    public function testClaimsConvert()
    {
        $claims_convert_response = $this->object->claims_convert($this->claims_convert_request_file)->body();

        $this->assertObjectHasAttribute('meta', $claims_convert_response);
        $this->assertObjectHasAttribute('data', $claims_convert_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::create_identity
     */
    public function testCreateIdentity()
    {
        $create_identity_response = $this->object->create_identity($this->identity_request)->body();

        $this->assertObjectHasAttribute('meta', $create_identity_response);
        $this->assertObjectHasAttribute('data', $create_identity_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::update_identity
     */
    public function testUpdateIdentity()
    {
        $update_identity_request = $this->identity_request;
        $update_identity_request["email"] = "tim@pokitdok.com";

        $update_identity_response = $this->object->update_identity(
            "054859ae-7152-468b-b45e-0f5c87d2d867", $update_identity_request)->body();

        $this->assertObjectHasAttribute('meta', $update_identity_response);
        $this->assertObjectHasAttribute('data', $update_identity_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::identity
     */
    public function testIdentityUUID()
    {
        $identity_response = $this->object->identity("054859ae-7152-468b-b45e-0f5c87d2d867")->body();

        $this->assertObjectHasAttribute('meta', $identity_response);
        $this->assertObjectHasAttribute('data', $identity_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::identity
     */
    public function testIdentitySearch()
    {
        $identity_response = $this->object->identity(array("first_name" => "Oscar", "last_name" => "Whitmire"))->body();

        $this->assertObjectHasAttribute('meta', $identity_response);
        $this->assertObjectHasAttribute('data', $identity_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::identity_history
     */
    public function testIdentityHistoryUUID()
    {
        $identity_history_response = $this->object->identity_history("054859ae-7152-468b-b45e-0f5c87d2d867")->body();

        $this->assertObjectHasAttribute('meta', $identity_history_response);
        $this->assertObjectHasAttribute('data', $identity_history_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::identity_history
     */
    public function testIdentityHistoryUUIDVersion()
    {
        $identity_history_response = $this->object->identity_history("054859ae-7152-468b-b45e-0f5c87d2d867", 1)->body();

        $this->assertObjectHasAttribute('meta', $identity_history_response);
        $this->assertObjectHasAttribute('data', $identity_history_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::pharmacy_plans
     */
    public function testPharmacyPlans()
    {
        $pharmacy_plans_response = $this->object->pharmacy_plans(
            array("trading_partner_id" => "medicare_national", "plan_number" => "S5820003"))->body();

        $this->assertObjectHasAttribute('meta', $pharmacy_plans_response);
        $this->assertObjectHasAttribute('data', $pharmacy_plans_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::pharmacy_formulary
     */
    public function testPharmacyFormulary()
    {
        $pharmacy_formulary_response = $this->object->pharmacy_formulary(
            array("trading_partner_id" => "MOCKPAYER", "plan_number" => "S5820003", "ndc" => "59310-579-22"))->body();

        $this->assertObjectHasAttribute('meta', $pharmacy_formulary_response);
        $this->assertObjectHasAttribute('data', $pharmacy_formulary_response);
    }

    /**
     * @covers PokitDok\Platform\PlatformClient::request
     */
    public function testRequest()
    {
        $eligibility = $this->object->request(
            'POST',
            '/eligibility/',
            $this->eligibility_request,
            "application/json")->body();

        $this->assertObjectHasAttribute('meta', $eligibility);
        $this->assertObjectHasAttribute('data', $eligibility);
        $this->assertObjectHasAttribute('provider', $eligibility->data);
        $this->assertObjectHasAttribute('subscriber', $eligibility->data);
        $this->assertObjectHasAttribute('payer', $eligibility->data);
    }
}
