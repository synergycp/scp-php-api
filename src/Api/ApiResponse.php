<?php

namespace Scp\Api;

/**
 * API Response.
 */
class ApiResponse
{
    /**
     * @var int
     */
    const STATUS_OK = 1;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var int
     */
    public $status;

    /**
     * @var ApiRequest
     */
    public $request;

    /**
     * @param ApiRequest $request
     * @param string     $body
     * @param int        $status
     */
    public function __construct(ApiRequest $request, $body, $status)
    {
        $this->body = $body;
        $this->status = $status;
        $this->request = $request;

        // Force errors to show if they've happened.
        if ($this->decode()->error !== false) {
            throw new ApiResponseError($this);
        }
    }

    /**
     * @return \stdClass
     *
     * @throws JsonDecodingError
     */
    public function decode()
    {
        $resp = json_decode($this->body);
        if (!$resp) {
            $this->jsonError();
        }

        return $resp;
    }

    /**
     * @return string
     */
    public function raw()
    {
        return $this->body;
    }

    /**
     * @return string|void
     */
    public function error()
    {
        $decode = $this->decode();

        return $decode && isset($decode->error) ? $decode->error : null;
    }

    /**
     * @return stdClass|mixed
     */
    public function data()
    {
        $resp = $this->decode();

        /*
        if (!empty($resp->msgs))
            foreach ($resp->msgs as $msg)
                if ($msg->cat == 'danger')
                    return $msg->text;

        if (empty($resp->result))
            $resp->result = "success";
        */

        return $resp->data;
    }

    /**
     * @throws JsonDecodingError
     */
    private function jsonError()
    {
        static $errors = [
            JSON_ERROR_NONE => null,
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        ];
        $error = json_last_error();
        if (array_key_exists($error, $errors)) {
            throw new JsonDecodingError($errors[$error], $this->body);
        }
    }
}
