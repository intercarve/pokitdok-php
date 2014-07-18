<?php
// Copyright (C) 2014, All Rights Reserved, PokitDok, Inc.
// http://www.pokitdok.com
//
// Please see the LICENSE.txt file for more information.
// All other rights reserved.
//


namespace PokitDok\Common;


/**
 * Class Oauth2ApplicationClient General Oauth2 client implementing Basic Auth and client_credentials grant type
 *
 * @package PokitDok\Common
 */
class Oauth2ApplicationClient {

    const DEFAULT_TIMEOUT = 90;

    private $_client_id = '';
    private $_client_secret = '';
    private $_request_timeout = self::DEFAULT_TIMEOUT;
    private $_cert_file = '';

    private $_access_token = '';
    private $_access_token_expires = null;
    private $_access_token_result = null;
    private $_api_base_url = '';
    private $_api_token_url = '';
    private $_ch = null;

    /**
     * @var \PokitDok\Common\HttpResponse
     */
    private $_response = null;
    private $_status = 0;

    /**
     * @param string $id Client ID
     * @param string $secret Client Secret
     * @param int $request_timeout Timeout for API requests, default is self::DEFAULT_TIMEOUT
     * @param string $access_token_json JSON string with the access token result from previous request:
     *      {
     *          "access_token": "tOUwQvtoj3vYhbM1AOSDBjebnXjevMQeZjcYBYPL",     // required
     *          "token_type": "bearer",
     *          "expires": 1398735988,                                          // required
     *          "expires_in": 3600
     *      }
     * @param string $cert_file     Fully qualified path to trusted CA certificates file
     */
    public function __construct(
        $id,
        $secret,
        $request_timeout = self::DEFAULT_TIMEOUT,
        $access_token_json = null,
        $cert_file = '')
    {
        $this->_client_id = $id;
        $this->_client_secret = $secret;
        $this->_request_timeout = $request_timeout;

        if (isset($access_token_json)) {
            $this->setAccessToken($access_token_json);
        }

        $this->_cert_file = $cert_file;
    }

    /**
     * @return resource Curl resource handle
     * @throws \Exception On error initializing curl
     */
    private function get_handle()
    {
        $this->_ch = curl_init();
        if ($this->_ch === false) {
            throw new \Exception(curl_error($this->_ch), curl_errno($this->_ch));
        }

        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->_request_timeout);
        curl_setopt($this->_ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->_ch, CURLOPT_USERPWD, $this->_client_id .":". $this->_client_secret);
        if ($this->_cert_file === '') {
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
        } else {
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($this->_ch, CURLOPT_CAINFO, $this->_cert_file);
        }
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, 2);

        return $this->_ch;
    }

    /**
     * @return string JSON string of access token response
     *  {
     *      "access_token": "s8KYRJGTO0rWMy0zz1CCSCwsSesDyDlbNdZoRqVR",
     *      "token_type": "bearer",
     *      "expires": 1393350569,
     *      "expires_in": 3600
     *  }
     * @throws \Exception On error configure Curl access token request
     */
    private function retrieve_access_token()
    {
        if (!$this->isTokenExpired()) {
            return $this->_access_token_result;
        }

        $this->get_handle();
        curl_setopt($this->_ch, CURLOPT_URL, $this->_api_token_url);
        curl_setopt($this->_ch, CURLOPT_POST, true);
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        if ($this->_ch === false) {
            throw new \Exception(curl_error($this->_ch), curl_errno($this->_ch));
        }

        $result = curl_exec($this->_ch);
        if ($result === false) {
            throw new \Exception(curl_error($this->_ch), curl_errno($this->_ch));
        }
        $this->setAccessToken($result);
        curl_close($this->_ch);

        return $this->_access_token_result;
    }

    /**
     * @param string $access_token_json JSON string returned from retrieve_access_token()
     *  {
     *      "access_token": "s8KYRJGTO0rWMy0zz1CCSCwsSesDyDlbNdZoRqVR",
     *      "token_type": "bearer",
     *      "expires": 1393350569,
     *      "expires_in": 3600
     *  }
     * @return string JSON string of access token response
     * @throws \Exception On error returned from access token request
     */
    private function setAccessToken($access_token_json)
    {
        $this->_access_token = '';
        $this->_access_token_expires = null;
        $this->_access_token_result = json_decode($access_token_json);

        if (isset($this->_access_token_result->error)) {
            throw new \Exception($this->_access_token_result->error);
        } else {
            $this->_access_token = $this->_access_token_result->access_token;
            $this->_access_token_expires = $this->_access_token_result->expires;
        }

        return $this->_access_token_result;
    }

    /**
     * Initialize access token
     */
    public function init()
    {
        $this->retrieve_access_token();
    }

    /**
     * @param string $request_type HTTP verbs: 'GET', 'POST', 'PUT', etc.
     * @param string $request_path URL path to the API call
     * @param null $parameters Query parameters (array: formatted as query params or string: appended to URL path)
     * @param string $content_type MIME type of content
     * @return mixed HTTP status code returned
     * @throws \Exception On errors (>299 status response)
     */
    public function request($request_type, $request_path, $parameters = null, $content_type = '')
    {
        $this->_response = null;
        $this->_status = 0;

        $this->retrieve_access_token();
        $this->get_handle();
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, $request_type);

        $headers = array(sprintf('Authorization: Bearer %s', $this->_access_token));

        if ($request_type === "GET") {
            curl_setopt(
                $this->_ch,
                CURLOPT_URL,
                $this->_api_base_url .
                    $request_path .
                    (is_array($parameters) ? '?'. http_build_query($parameters) : $parameters)
            );
        } else {
            curl_setopt($this->_ch, CURLOPT_URL, $this->_api_base_url . $request_path);
            curl_setopt(
                $this->_ch,
                CURLOPT_POSTFIELDS,
                ($content_type === "application/json" ? json_encode($parameters) : $parameters)
            );
        }

        if ($content_type !== '') {
            $headers[] = sprintf('Content-Type:  %s', $content_type);
        }

        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);

        $response = curl_exec($this->_ch);

        $this->_response = new HttpResponse($response);
        $this->_status = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);

        curl_close($this->_ch);

        if ($this->_status > 299) {
            throw new \Exception(json_encode($this->_response->body()->data), $this->_status);
        }

        return $this->_status;
    }

    /**
     * @return bool True if access token is expired
     */
    public function isTokenExpired()
    {
        return (time() > ($this->_access_token_expires - $this->_request_timeout));
    }

    /**
     * @param string $api_base_url Base URL API endpoint
     */
    public function setApiBaseUrl($api_base_url)
    {
        $this->_api_base_url = $api_base_url;
    }

    /**
     * @return string Base URL API endpoint
     */
    public function getApiBaseUrl()
    {
        return $this->_api_base_url;
    }

    /**
     * @param string $api_token_url
     */
    public function setApiTokenUrl($api_token_url)
    {
        $this->_api_token_url = $api_token_url;
    }

    /**
     * @return string
     */
    public function getApiTokenUrl()
    {
        return $this->_api_token_url;
    }

    /**
     * @param int $request_timeout Time interval (seconds) to wait for request completion
     */
    public function setRequestTimeout($request_timeout)
    {
        $this->_request_timeout = $request_timeout;
    }

    /**
     * @return int Time interval (seconds) to wait for request completion
     */
    public function getRequestTimeout()
    {
        return $this->_request_timeout;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_access_token;
    }

    /**
     * @return null Unix timestamp
     */
    public function getAccessTokenExpires()
    {
        return $this->_access_token_expires;
    }

    /**
     * @return int HTTP status code
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return \PokitDok\Common\HttpResponse
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return mixed
     */
    public function getResponseHeader()
    {
        return $this->_response->header();
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->_response->body();
    }
}
