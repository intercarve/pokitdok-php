<?php
// Copyright (C) 2014, All Rights Reserved, PokitDok, Inc.
// http://www.pokitdok.com
//
// Please see the LICENSE.txt file for more information.
// All other rights reserved.
//

namespace PokitDok\Common;


use PokitDok\Platform\PlatformClient;

class HttpResponse {

    private $_response;
    private $_header_length;

    public function __construct($response, $header_length)
    {
        $this->_response = $response;
        $this->_header_length = $header_length;
    }

    /**
     * @return array
     */
    public function header()
    {
        $raw_header = substr($this->_response, 0, $this->_header_length);
        $lines = explode("\r\n", $raw_header);
        $headers = array();
        foreach ($lines as $line)
        {
            if (empty($line)) {
                continue;
            }
            $sep_pos = strpos($line, ':');
            if ($sep_pos !== false) {
                $headers[trim(substr($line, 0, $sep_pos))] =
                    trim(substr($line, $sep_pos+1, strlen($line) - ($sep_pos+1)));
            } else {
                $headers[trim($line)] = '';
            }
        }

        return $headers;
    }

    /**
     * @return mixed
     */
    public function body()
    {
        $raw_body = substr($this->_response, $this->_header_length);

        $body = json_decode($raw_body);

        return ($body === false ? $raw_body : $body);
    }
}