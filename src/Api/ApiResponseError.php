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
     * @param string      $message
     * @param int         $code
     * @param \Exception  $previous
     * @param ApiResponse $response
     */
    public function __construct($message, $code, $previous, ApiResponse $response = null)
    {
        $this->response = $response ?: ($previous ? $previous->response : null);
        $args = [
            $this->response ? sprintf(
                'Error %d with HTTP %s %s: %s',
                $this->response->status,
                $this->response->request->method,
                $this->response->request->url,
                $this->response->error()
            ) : $message,
            $code,
        ];

        if ($previous) {
            $args[] = $previous;
        }

        call_user_func_array(['parent', '__construct'], $args); 
    }
}
