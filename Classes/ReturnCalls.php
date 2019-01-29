<?php


/**
 * Class ReturnCalls
 * @author Marek Mazur
 */
class ReturnCalls
{
    /**
     * Statuses
     */
    const STATUSID_SUCCESS = 'success';
    const STATUSID_WARNING = 'warning';
    const STATUSID_ERROR = 'error';

    /**
     * @var null|string
     */
    private $message = null;

    /**
     * @var null|int
     */
    private $statusId = null;

    /**
     * @var null|string
     */
    private $error = null;

    /**
     * @var null|string
     */
    private $data = null;

    /**
     * @var null|int
     */
    private $code = null;

    /**
     * Construct method
     */
    public function __construct()
    {
    }

    /**
     * Get response.
     *
     * 'message' message to the user.
     * 'statusid' status of the response e.g. success, error. (constants from this class)
     * 'error' Error code (replace with 'code')
     * 'code' The status code (constants from this class)
     * 'data' The data or body of the response.
     */
    public function getResponse()
    {
        $response = [
            'message' => $this->getMessage(),
            'statusId' => $this->getStatusId(),
            'error' => $this->getError(), // Replace with code
            'code' => $this->getCode(),
            'data' => $this->getData(),
        ];

        // Convert to json and remove empty elements.
        echo json_encode(array_filter($response));

        return;
    }

    /**
     * Get response message
     */
    private function getMessage()
    {
        return $this->message;
    }

    /**
     * Set response message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = trim($message);
    }

    /**
     * Get status id. If the status is not set we return by defualt STATUSID_SUCCESS
     */
    public function getStatusId()
    {
        return !$this->statusId ? self::STATUSID_SUCCESS : $this->statusId;
    }

    /**
     * Set response status id.
     * Allowed statuses: STATUSID_ERROR, STATUSID_SUCCESS, STATUSID_WARNING
     *
     * @param string $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = in_array($statusId,
            [self::STATUSID_ERROR, self::STATUSID_SUCCESS, self::STATUSID_WARNING]) ? $statusId : $this->statusId;
    }

    /**
     * Get error code
     */
    private function getError()
    {
        return $this->error;
    }

    /**
     * Set error code
     *
     * @param int $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Get the value of Data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of Data
     *
     * @param mixed data
     *
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of Code
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of Code
     *
     * @param mixed code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

}
