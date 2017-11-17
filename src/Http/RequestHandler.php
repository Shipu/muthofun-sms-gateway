<?php

namespace Shipu\MuthoFun\Http;

use GuzzleHttp\Client;

class RequestHandler
{
    /**
     * Guzzle http client object
     *
     * @var Client
     */
    public $http;

    public function __construct($base_url)
    {
        $this->http = new Client(['base_uri' => $base_url, 'timeout' => 3.0, 'decode_content' => false]);
    }
}
