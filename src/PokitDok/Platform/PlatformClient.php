<?php
// Copyright (C) 2014, All Rights Reserved, PokitDok, Inc.
// http://www.pokitdok.com
//
// Please see the LICENSE.txt file for more information.
// All other rights reserved.
//
//	PokitDok Platform Client for PHP
//		Consume the REST based PokitDok platform API
//		https://platform.pokitdok.com/login#/documentation


namespace PokitDok\Platform;

use PokitDok\Common\Oauth2ApplicationClient;

/**
 * Class PlatformClient The PokitDok  API allows you to perform X12 transactions,
 *  find healthcare providers and get information on health care procedure pricing.
 * See docs here: https://platform.pokitdok.com/documentation/v4#/
 *
 * Common Query parameters:
 * async	Whether the API call is asynchronous. For Resources that offer both synchronous and asynchronous operation,
 *          a boolean can be used for this parameter to specify which mode of operation you desire; if the async
 *          parameter is omitted, the synchronous mode will be used. For POST requests, the async parameter should be
 *          included along with other JSON data being POSTed. When async is true, the API client has the option of
 *          including a callback URL so that it can be notified when the asynchronous processing is complete.
 * dir	    The direction that a list is sorted, ascending or descending (only for collection requests)
 * limit	The number of Resources to return in a list (only for collection requests)
 * offset	The number of Resources to skip when paging through a list (only for collection requests)
 * sort	    The field to sort the list of Resources by (only for collection requests)
 *
 * @package PokitDok\Platform
 */
class PlatformClient extends Oauth2ApplicationClient
{
    const POKITDOK_PLATFORM_API_SITE = 'https://platform.pokitdok.com';

    const POKITDOK_PLATFORM_API_TOKEN_URL = '/oauth2/token';
    const POKITDOK_PLATFORM_API_VERSION_PATH = '/api/v4';

    const POKITDOK_PLATFORM_API_ENDPOINT_ELIGIBILITY = '/eligibility/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PROVIDERS = '/providers/';
    const POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS = '/claims/';
    const POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS_STATUS = '/claims/status';
    const POKITDOK_PLATFORM_API_ENDPOINT_ENROLLMENT = '/enrollment/';
    const POKITDOK_PLATFORM_API_ENDPOINT_DEDUCTIBLE = '/deductible/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PAYERS = '/payers/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PRICE_INSURANCE = '/prices/insurance';
    const POKITDOK_PLATFORM_API_ENDPOINT_PRICE_CASH = '/prices/cash';
    const POKITDOK_PLATFORM_API_ENDPOINT_ACTIVITIES = '/activities/';
    const POKITDOK_PLATFORM_API_ENDPOINT_FILES = '/files/';
    const POKITDOK_PLATFORM_API_ENDPOINT_TRADING_PARTNERS = '/tradingpartners/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PLANS = '/plans/';
    const POKITDOK_PLATFORM_API_ENDPOINT_REFERRALS = '/referrals/';
    const POKITDOK_PLATFORM_API_ENDPOINT_AUTHORIZATIONS = '/authorizations/';
    const POKITDOK_PLATFORM_API_ENDPOINT_SCHEDULERS = '/schedule/schedulers/';
    const POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENT_TYPES = '/schedule/appointmenttypes/';
    const POKITDOK_PLATFORM_API_ENDPOINT_SLOTS = '/schedule/slots/';
    const POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENTS = '/schedule/appointments/';


    private $_usage = null;
    private $_version_path = self::POKITDOK_PLATFORM_API_VERSION_PATH;

    private function applyResponse()
    {
        $json_response = $this->getResponseBody();
        if (!($json_response instanceof \stdClass)) {
            throw new \Exception($this->getResponseBody(), $this->getStatus());
        }
        if (isset($json_response->errors)) {
            throw new \Exception(json_encode($json_response->errors), $this->getStatus());
        }
        $this->_usage = $json_response->meta;

        return $this->getResponse();
    }

    /**
     * @param string $id PokitDok API Client ID
     * @param string $secret PokitDok API Client Secret
     * @param int $request_timeout Timeout for all requests (default is 90(s))
     * @param string $access_token_json JSON string of access token response for saved authentication token
     * @param string $cert_file Full path to certificate file containing CA certs
     */
    public function __construct(
        $id,
        $secret,
        $request_timeout = self::DEFAULT_TIMEOUT,
        $access_token_json = null,
        $cert_file = '')
    {
        parent::__construct(
            $id,
            $secret,
            $request_timeout,
            $access_token_json,
            $cert_file);

        $this->setApiBaseUrl(self::POKITDOK_PLATFORM_API_SITE . $this->_version_path);
        $this->setApiTokenUrl(self::POKITDOK_PLATFORM_API_SITE . self::POKITDOK_PLATFORM_API_TOKEN_URL);
    }

    /**
     * @param string $version_path
     */
    public function setVersionPath($version_path)
    {
        $this->_version_path = $version_path;
        $this->setApiBaseUrl(self::POKITDOK_PLATFORM_API_SITE . $this->_version_path);
    }

    /**
     * @return string
     */
    public function getVersionPath()
    {
        return $this->_version_path;
    }

    /**
     * Usage statistics for most recent request
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#overview
     *
     * @return \stdClass Object
     * 	    rate_limit_cap, {int} The amount of requests available per hour
     * 	    rate_limit_reset, {int} The time (Unix Timestamp) when the rate limit amount resets
     *		rate_limit_amount, {int} The amount of requests made during the current rate limit period
     *		credits_billed, {int} The amount of credits billed for this request
     *		credits_remaining, {int} The amount of credits remaining on your API account
     *		processing_time, {int} The time to process the request in milliseconds
     *		next, {string}, A url pointing to the next page of results
     *		previous, {string} A url pointing to the previous page of results
     * @throws \Exception On data or API errors
     */
    public function usage()
    {
        if (!isset($this->_usage)) {
            $this->eligibility(
                array(
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
                'trading_partner_id' => 'MOCKPAYER'
            ));
        }

        return $this->_usage;
    }

    /**
     * Retrieve the data for a specified provider id or query parameters
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#providers
     *
     * @param mixed $providers_request String of the PokitDok UUID for the provider OR Array of query parameters
     *  Query parameters:
     * 	    organization_name, The business practice name
     *		first_name, Provider first name
     *	    last_name, Provider first name
     *	    specialty, Provider specialty name from NUCC/NPI taxonomy
     *	    city, Provider city
     *	    state, Provider state
     *	    zipcode, Provider 5-digit zip code
     *	    radius, Search distance from geographic centerpoint, with unit (e.g. “1mi” or “50mi”)
     *		    (Only used when city, state, or zipcode is passed)
     * @return \PokitDok\Common\HttpResponse Response object with providers data,
     *      see API documentation on https://platform.pokitdok.com/
     * @throws \Exception On data or API errors
     */
    public function providers($providers_request = '')
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_PROVIDERS,
            $providers_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Determine eligibility via an EDI 270 Request For Eligibility.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#eligibility
     *
     * @param array $eligibility_request Array representing an EDI 270 Request For Eligibility as JSON
     * @return \PokitDok\Common\HttpResponse Response object with eligibility data
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function eligibility(array $eligibility_request)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_ELIGIBILITY,
            $eligibility_request,
            "application/json"
        );

        if (empty($eligibility_request)) {
            $json_response = $this->getResponseBody();
            if (!($json_response instanceof \stdClass)) {
                throw new \Exception($this->getResponseBody(), $this->getStatus());
            }
            $this->_usage = $json_response->meta;
            return $this->getResponse();
        }

        return $this->applyResponse();
    }

    /**
     * Create a new claim, via the filing of an EDI 837 Professional Claims, to the designated Payer.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#claims
     *
     * @param array $claims_request Array representing EDI 837 Professional Claim as JSON
     * @return \PokitDok\Common\HttpResponse Response object with claims data
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function claims(array $claims_request)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS,
            $claims_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Ascertain the status of the specified claim, via the filing of an EDI 276 Claims Status.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#claimstatus
     *
     * @param array $claims_status Array representing EDI 276 Claims Status as JSON
     * @return \PokitDok\Common\HttpResponse Response object with claimsStatus data,
     *      see API documentation on https://platform.pokitdok.com/
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function claimsStatus(array $claims_status)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS_STATUS,
            $claims_status,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * File an EDI 834 benefit enrollment.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#enrollment
     *
     * @param array $enrollment_request representing 834 benefit enrollment as JSON
     * @return \PokitDok\Common\HttpResponse Response object with enrollment data
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function enrollment(array $enrollment_request)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_ENROLLMENT,
            $enrollment_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Return a list of insurance prices for a given procedure (by CPT Code) in a given region (by ZIP Code).
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#insuranceprices
     *
     * @param array $price_insurance_request Array of price query parameters
     *  Query parameters:
     * 	    cpt_code, {string} The CPT code of the procedure in question.
     *		zip_code, {string} Postal code in which to search for procedures
     * @return \PokitDok\Common\HttpResponse Response object with priceInsurance data,
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function pricesInsurance(array $price_insurance_request)
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_PRICE_INSURANCE,
            $price_insurance_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Return a list of cash prices for a given procedure (by CPT Code) in a given region (by ZIP Code).
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#cashprices
     *
     * @param array $price_cash_request Array of price query parameters
     *  Query parameters:
     * 	    cpt_code, {string} The CPT code of the procedure in question.
     *		zip_code, {string} Postal code in which to search for procedures
     * @return \PokitDok\Common\HttpResponse Response object with priceCash data,
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function pricesCash(array $price_cash_request)
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_PRICE_CASH,
            $price_cash_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Call the activities endpoint to get a listing of current activities,
     * a query string parameter ‘parent_id’ may also be used with this API to get information about
     * sub-activities that were initiated from a batch file upload.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#activities
     *
     * @param mixed $activities_request String of the PokitDok ID for the activity OR Array of query parameters
     *  Query parameters:
     *  _id, {string} ID of this Activity
     *  name, {string} Activity name
     *  callback_url, {string} URL that will be invoked to notify the client application that this Activity has completed.
     *  	We recommend that you always use https for callback URLs used by your application.
     *  file_url, {string} URL where batch transactions that were uploaded to be processed within this activity are stored.
     *  	X12 files uploaded via the /files endpoint are stored here.
     *  history, {list} Historical status of the progress of this Activity
     *  state, {dict} Current state of this Activity
     *  transition_path, {list} The list of state transitions that will be used for this Activity.
     *  remaining_transitions, {list} The list of remaining state transitions that the activity has yet to go through.
     *  parameters, {dict} The parameters that were originally supplied to the activity
     *  units_of_work, {int} The number of ‘units of work’ that the activity is operating on.
     *  	This will typically be 1 for real-time requests like /eligibility.
     *  	When uploading batch X12 files via the /files endpoint, this will be the number of ‘transactions’ within
     *  	that file.  For example, if a client application POSTs a file of 20 eligibility requests to the /files API,
     *  	the units_of_work value for that activity will be 20 after the X12 file has been analyzed.  If an activity
     *  	does show a value greater than 1 for units_of_work, the client application can fetch detailed information
     *  	about each one of the activities processing those units of work by using the
     *  	/activities/?parent_id=&lt;activity_id&gt; API
     * @return \PokitDok\Common\HttpResponse Response object with activities data
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function activities($activities_request = '')
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_ACTIVITIES,
            $activities_request
        );

        return $this->applyResponse();
    }

    /**
     * Submit X12 formatted EDI file for batch processing.
     *
     * @param string $edi_file full path and filename of EDI file to submit
     * @param string $trading_partner_id The trading partner id
     * @param string $callback_url Optional notification url to be called when the asynchronous processing is complete
     * @return \PokitDok\Common\HttpResponse
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function files($edi_file, $trading_partner_id, $callback_url = null)
    {
        $post_params = array();
        $post_params['file'] = "@". $edi_file .";type=application/EDI-X12;filename=". basename($edi_file);
        $post_params['trading_partner_id'] = $trading_partner_id;
        if ($callback_url !== null) {
            $post_params['callback_url'] = $callback_url;
        }

        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_FILES,
            $post_params
        );

        return $this->applyResponse();
    }

    /**
     * Retrieve a list of trading partners or submit an id to get info for a
     * specific trading partner.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#tradingpartners
     *
     * @param string $trading_partner_id id of the requested trading partner
     *                                   leave blank for full listing
     * @return \PokitDok\Common\HttpResponse Response object with trading partner data
     * @throws \Exception On HTTP errors (status > 299)
     */
     public function trading_partners($trading_partner_id = '')
     {
       $this->request(
           'GET',
           self::POKITDOK_PLATFORM_API_ENDPOINT_TRADING_PARTNERS,
           $trading_partner_id,
           "application/json"
       );

       return $this->applyResponse();
     }

     /**
      * Retrieve data on plans based on the parameters given.
      * See docs here: https://platform.pokitdok.com/documentation/v4#/#plans
      *
      * @param array $plans_request Array of query parameters.
      * Query Parameters:
      *   trading_partner_id, {string} The trading partner id of the payer
      *     offering the plan.
      *   county, {string} The county in which the plan is available.
      *   state, {string} The state in which the plan is available.
      *   plan_id, {string} The identifier for the plan.
      *   plan_type, {string} The type of plan (e.g. EPO, PPO, HMO, POS).
      *   plan_name, {string} The name of the plan.
      *   metallic_level, {string} The metal level of the plan.
      * @return \PokitDok\Common\HttpResponse Response object with plans data,
      *         see documentation on https://platform.pokitdok.com/
      * @throws \Exception On HTTP errors (status > 299)
      */
      public function plans(array $plans_request = array())
      {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_PLANS,
            $plans_request,
            "application/json"
        );

        return $this->applyResponse();
      }

    /**
     * Request approval for a referral to another health care provider.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#referrals
     *
     * @param array $referral_request Array representing a referral request
     * @return \PokitDok\Common\HttpResponse Response object with referral data,
     *      see API documentation on https://platform.pokitdok.com/
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function referrals(array $referral_request)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_REFERRALS,
            $referral_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Submit an authorization request.
     * See docs here: https://platform.pokitdok.com/documentation/v4#/#authorizations
     *
     * @param array $authorization_request Array representing an authorization request
     * @return \PokitDok\Common\HttpResponse Response object with authorization data,
     *      see API documentation on https://platform.pokitdok.com/
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function authorizations(array $authorization_request)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_AUTHORIZATIONS,
            $authorization_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Get a list of (or specific by UUID) the supported Scheduling Systems and their details.
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param string $schedulers_request string The UUID of the Scheduler (scheduler_uuid), empty gets the entire list
     * @return \PokitDok\Common\HttpResponse Response object with scheduler details,
     *      see API documentation on https://platform.pokitdok.com/
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function schedulers($schedulers_request = '')
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_SCHEDULERS,
            $schedulers_request
        );

        return $this->applyResponse();
    }

    /**
     * Get a list of (or specific by UUID) the supported Appointment Types and their details.
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param string $appointment_types_request The UUID of the Appointment Type (appointment_type_uuid), empty gets
     *          the entire list
     * @return \PokitDok\Common\HttpResponse Response object with appointment type details
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function appointment_types($appointment_types_request = '')
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENT_TYPES,
            $appointment_types_request
        );

        return $this->applyResponse();
    }

    /**
     * Create an open slot given details. Only valid use is for PokitDok Scheduler only providers.
     * {
     *      "pd_provider_uuid": "fd0d75d2-6285-4ecc-aca0-017f0f313bd6",
     *      "location": [32.7844314, -79.9994895], // geo location
     *      "appointment_type": "ATYP1", // appointment type name
     *      "start_date": "2014-03-17T08:00:00", // formatted as ISO8601
     *      "end_date": "2014-03-17T09:00:00" // formatted as ISO8601
     * }
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param array $create_slot_request Array of open slot details
     * @return \PokitDok\Common\HttpResponse Response object of the booked Appointment
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function create_slot(array $create_slot_request)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENTS,
            $create_slot_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Query for open appointment slots (using pd_provider_uuid and location) or booked appointments
     *      (using patient_uuid) given query parameters, or get a single appointment given a UUID (pd_appointment_uuid)
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param array|string $appointments_request Array of query parameters or string of the UUID (pd_appointment_uuid)
     * @return \PokitDok\Common\HttpResponse Response object with Open Slot or Appointment details
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function appointments($appointments_request = '')
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENTS,
            $appointments_request
        );

        return $this->applyResponse();
    }

    /**
     * Book appointment for an open slot. Put data contains patient attributes and description.
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param string $book_appointment_uuid The Appointment UUID (pd_appointment_uuid) to book
     * @param array $book_appointment_request Array of the patient attributes and description
     * @return \PokitDok\Common\HttpResponse Response object of the booked Appointment
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function book_appointment($book_appointment_uuid, array $book_appointment_request)
    {
        $this->request(
            'PUT',
            self::POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENTS. $book_appointment_uuid,
            $book_appointment_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Update appointment description. Put data contains description.
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param string $update_appointment_uuid The Appointment UUID (pd_appointment_uuid) to update
     * @param array $update_appointment_request Array of the description
     * @return \PokitDok\Common\HttpResponse Response object of the booked Appointment
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function update_appointment($update_appointment_uuid, array $update_appointment_request)
    {
        $this->request(
            'PUT',
            self::POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENTS. $update_appointment_uuid,
            $update_appointment_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * Cancel an Appointment.
     * See docs here: https://platform.pokitdok.com/documentation/v4#scheduling
     *
     * @param string $cancel_appointment_uuid The Appointment UUID (pd_appointment_uuid) to cancel
     * @return \PokitDok\Common\HttpResponse Response object of the booked Appointment
     * @throws \Exception On HTTP errors (status > 299)
     */
    public function cancel_appointment($cancel_appointment_uuid)
    {
        $this->request(
            'DELETE',
            self::POKITDOK_PLATFORM_API_ENDPOINT_APPOINTMENTS,
            $cancel_appointment_uuid
        );
    }
}
