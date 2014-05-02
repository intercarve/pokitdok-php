<?php
/**
 * Copyright (c) 2014 PokitDok Inc. See LICENSE.txt for details.
 *
 * Author: timdunlevy
 * Date: 4/11/14
 * Time: 11:18 AM
 */


namespace PokitDok\Platform;

use PokitDok\Common\Oauth2ApplicationClient;

/**
 * Class PlatformClient The PokitDok  API allows you to perform X12 transactions,
 *  find healthcare providers and get information on health care procedure pricing.
 *
 * @package PokitDok\Platform
 */
class PlatformClient extends Oauth2ApplicationClient
{
    const POKITDOK_PLATFORM_API_SITE = 'https://platform.pokitdok.com';

    const POKITDOK_PLATFORM_API_TOKEN_URL = '/oauth2/token';
    const POKITDOK_PLATFORM_API_VERSION_PATH = '/api/v3';

    const POKITDOK_PLATFORM_API_ENDPOINT_ELIGIBILITY = '/eligibility/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PROVIDERS = '/providers/';
    const POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS = '/claims/';
    const POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS_STATUS = '/claims/status/';
    const POKITDOK_PLATFORM_API_ENDPOINT_ENROLLMENT = '/enrollment/';
    const POKITDOK_PLATFORM_API_ENDPOINT_DEDUCTIBLE = '/deductible/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PAYERS = '/payers/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PRICE_INSURANCE = '/price/insurance/';
    const POKITDOK_PLATFORM_API_ENDPOINT_PRICE_CASH = '/price/cash/';
    const POKITDOK_PLATFORM_API_ENDPOINT_ACTIVITIES = '/activities/';


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
     * @return \stdClass Object
     */
    public function usage()
    {
        if (!isset($this->_usage)) {
            $this->eligibility(array());
        }

        return $this->_usage;
    }

    /**
     * @param mixed $providers_request String of the PokitDok UUID for the provider OR Array of query parameters
     * @throws \Exception On data or API errors
     * @return \PokitDok\Common\HttpResponse Response object with providers data,
     *      see API documentation on https://platform.pokitdok.com/
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
     * @param array $eligibility_request Array of eligibility endpoint query parameters
     * @throws \Exception
     * @return \PokitDok\Common\HttpResponse Response object with eligibility data,
     *      see API documentation on https://platform.pokitdok.com/
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
     * @param string $claims_file_name Fully qualified path to EDI 837 Professional Claim file
     * @return \PokitDok\Common\HttpResponse Response object with claims data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function claims($claims_file_name)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_CLAIMS,
            array('claim' => "@". $claims_file_name .";type=text/plain;filename=". basename($claims_file_name))
        );

        return $this->applyResponse();
    }

    /**
     * @param array $claims_status Array of claims/status endpoint query parameters
     * @return \PokitDok\Common\HttpResponse Response object with claimsStatus data,
     *      see API documentation on https://platform.pokitdok.com/
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
     * @param string $enrollment_file_name Fully qualified path to EDI 834 benefit enrollment file
     * @return \PokitDok\Common\HttpResponse Response object with enrollment data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function enrollment($enrollment_file_name)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_ENROLLMENT,
            array(
                'enrollment' =>
                    "@". $enrollment_file_name .";type=text/plain;filename=". basename($enrollment_file_name)
        ));

        return $this->applyResponse();
    }

    /**
     * @param string $deductible_file_name Fully qualified path to an EDI 270 file
     * @return \PokitDok\Common\HttpResponse Response object with deductible data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function deductible($deductible_file_name)
    {
        $this->request(
            'POST',
            self::POKITDOK_PLATFORM_API_ENDPOINT_DEDUCTIBLE,
            array(
                'enrollment' =>
                    "@". $deductible_file_name .";type=text/plain;filename=". basename($deductible_file_name)
        ));

        return $this->applyResponse();
    }

    /**
     * @param array $payers_request Array of payer query parameters
     * @return \PokitDok\Common\HttpResponse Response object with payers data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function payers(array $payers_request)
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_PAYERS,
            $payers_request,
            "application/json"
        );

        return $this->applyResponse();
    }

    /**
     * @param array $price_insurance_request Array of price query parameters
     * @return \PokitDok\Common\HttpResponse Response object with priceInsurance data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function priceInsurance(array $price_insurance_request)
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
     * @param array $price_cash_request Array of price query parameters
     * @return \PokitDok\Common\HttpResponse Response object with priceCash data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function priceCash(array $price_cash_request)
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
     * @param mixed $activities_request String of the PokitDok ID for the activity OR Array of query parameters
     * @return \PokitDok\Common\HttpResponse Response object with activities data,
     *      see API documentation on https://platform.pokitdok.com/
     */
    public function activities($activities_request = '')
    {
        $this->request(
            'GET',
            self::POKITDOK_PLATFORM_API_ENDPOINT_ACTIVITIES,
            $activities_request,
            "application/json"
        );

        return $this->applyResponse();
    }
}

