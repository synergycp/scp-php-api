<?php

namespace Scp\Api;

/**
 * API Request.
 */
class ApiRequest
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $url;

    /**
     * @param string $method
     * @param string $url
     */
    public function __construct($method, $url)
    {
        $this->url = $url;
        $this->method = $method;
    }
}
