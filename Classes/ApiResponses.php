<?php

use Slim\Http\Response;

use function FastRoute\TestFixtures\empty_options_cached;

/**
 * Class ApiResponses
 * @package Classes
 */
class ApiResponses
{
    // Ok
    const HTTP_200_CODE = 200;
    // New resource has been created
    const HTTP_201_CODE = 201;
    // Bad request
    const HTTP_400_CODE = 400;
    const HTTP_400_MESSAGE = "Bad request";
    // Unauthorised
    const HTTP_401_CODE = 401;
    const HTTP_401_MESSAGE = "Unauthorised";
    // Forbidden
    const HTTP_403_CODE = 403;
    const HTTP_403_MESSAGE = "Forbidden";
    // Not found
    const HTTP_404_CODE = 404;
    const HTTP_404_MESSAGE = "Not Found";
    // Precondition Failed
    const HTTP_412_CODE = 412;
    const HTTP_412_MESSAGE = "Precondition Failed";
    // Internal Server Error
    const HTTP_500_CODE = 500;
    const HTTP_500_MESSAGE = "Internal Server Error";
    // Service Unavailable
    const HTTP_503_CODE = 503;
    const HTTP_503_MESSAGE = "Service Unavailable";

    const UNDEFINED_ERROR = 0;
    const INVALID_TOKEN_CODE = 1;
    const ACCOUNT_INACTIVE = 20;
    const ACCOUNT_EXPIRED = 21;
    const INVALID_LANG_CODE_CODE = 3;
    const INVALID_DOMAIN_CODE = 4;
    const INVALID_SUPPLIER_TOKEN_CODE = 5;
    const INVALID_GUID_CODE = 6;
    const MISSING_TOKEN_CODE = 11;
    const MISSING_SESSION_ID_CODE = 12;
    const MISSING_LANG_CODE_CODE = 13;
    const MISSING_DOMAIN_CODE = 14;
    const MISSING_SUPPLIER_TOKEN_CODE = 15;
    const MISSING_GUID_CODE = 16;
    const MISSING_SEGMENT_CODE = 17;
    const INVALID_URLENDPOINT = 18;
    const SUPPLIER_ERROR = 19;
    const ENGINE_NOT_AVAILBALE = 22;
    const SEGMENTS_LIMIT_CODE = 23;
    const SEGMENTS_SIZE_CODE = 24;
    const AWAITING_TRANSLATION = 25;
    const MISSING_FILE = 26;
    const MISSING_FILE_TYPE = 29;
    const INVALID_FORMAT_LENGTH = 30;
    const FILE_NOT_BASE64_ENCODED = 31;
    const ERROR_CONTENT = 27;
    const ERROR_ACCESSING_FILE = 28;

    private $data = null;
    private $message = null;
    private $statusCode = null;
    private $code = null;
    private $timestamp = null;
    private $response = null;

    /**
     * ApiResponses constructor.
     */
    public function __construct()
    {
    }

    public function get()
    {
        // Set the skeleton
        $response = [
            'success' => $this->getData() ? true : false,
            'error' => $this->getError(),
            'data' => $this->getData()
        ];

        // If error is set and the http default response code is not reset (from 200) use the code error for http response code
        // $statusCode = $this->getError() && $this->getHttpCode() == self::HTTP_200_CODE ? $this->getStatusCode() : $this->getHttpCode();

        $response = $this->getResponse()->withJson($response, $this->getStatusCode());

        return $response;
    }

    public function getToJsonArray()
    {
        // Set the skeleton
        $response = [
            'success' => $this->getData() ? true : false,
            'error' => $this->getError(),
            'data' => $this->getData()
        ];

        return $response;
    }

    /**
     * @return null|array
     */
    public function getError()
    {
        // Set error if exists
        if (!empty($this->getStatusCode()) && !empty($this->getMessage())) {
            $error = [
                'statusCode' => $this->getStatusCode(),
                'code' => $this->getCode(),
                'timestamp' => $this->getTimestamp() ? $this->getTimestamp() : time(),
                'message' => $this->getMessage()
            ];

            return $error;
        }

        return null;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        $statusCode = empty($this->statusCode) ? self::HTTP_200_CODE : $this->statusCode;

        return $statusCode;
    }

    /**
     * @param int $code
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    /**
     * @return null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param null $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param null $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
}