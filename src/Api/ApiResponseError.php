<?php

namespace Scp\Api;

/**
 * API Response Error.
 */
class ApiResponseError
extends ApiError
{
    /**
     * @var ApiResponse
     */
    public $response;

    /**
     * @param ApiResponse $response
     */
    public function __construct(ApiResponse $response)
    {
        $this->response = $response;

        parent::__construct(sprintf(
            'Error %d with HTTP %s %s: %s',
            $this->response->status,
            $this->response->request->method,
            $this->response->request->url,
            $this->response->error()
        ));
    }
}
