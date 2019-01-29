<?php

/**
 * Class RequestLog
 * @author Marek Mazur
 */
class RequestLog extends Database
{
    /**
     * @var null|int
     */
    private $id = null;

    /**
     * @var null|int
     */
    private $methodId = null;

    /**
     * @var null|int
     */
    private $consumerAccountId = null;

    /**
     * @var null|int
     */
    private $supplierAccountId = null;

    /**
     * @var null|string
     */
    private $timeIn = null;

    /**
     * @var null|string
     */
    private $timeOut = null;

    /**
     * @var null|string
     */
    private $timeMs = null;

    /**
     * @var null|string
     */
    private $request = null;

    /**
     * @var null|string
     */
    private $response = null;

    /**
     * @var null|int
     */
    private $httpCode = null;

    /**
     * @var null|string
     */
    private $src = null;

    /**
     * @var null|string
     */
    private $trg = null;

    /**
     * @var null|string
     */
    private $engineId = null;

    /**
     * Groups constructor.
     * @param null|int $id
     */
    public function __construct($id = null)
    {
        $this->setTimeIn($this->getCurrentMySqlTime());
        $this->setTimeMs(microtime(true));

        if ($id) {
            $this->setId($id);
            $this->set($this->get());
        }
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        $query = 'INSERT INTO
                        requestlog(
                            methodid,
                            consumeraccountid,
                            supplieraccountid,
                            timein,
                            timeout,
                            timems,
                            httpcode,
                            request,
                            response,
                            src,
                            trg,
                            engineid
                    )
                    VALUES (
                        :methodid,
                        :consumeraccountid,
                        :supplieraccountid,
                        :timein,
                        :timeout,
                        :timems,
                        :httpcode,
                        :request,
                        :response,
                        :src,
                        :trg,
                        :engineid
                    )';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
        $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
        $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
        $this->bindValue(':timein', $this->getTimeIn(), PDO::PARAM_STR);
        $this->bindValue(':timeout', empty($this->getTimeOut()) ? $this->getCurrentMySqlTime() : $this->getTimeOut(),
            PDO::PARAM_STR);
        $this->bindValue(':timems', empty($this->getTimeMs()) ? null : microtime(true) - $this->getTimeMs(),
            PDO::PARAM_STR);
        $this->bindValue(':httpcode', $this->getHttpCode(), PDO::PARAM_INT);
        $this->bindValue(':request', $this->getRequest(), PDO::PARAM_STR);
        $this->bindValue(':response', $this->getResponse(), PDO::PARAM_STR);
        $this->bindValue(':src', $this->getSrc(), PDO::PARAM_STR);
        $this->bindValue(':trg', $this->getTrg(), PDO::PARAM_STR);
        $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);

        $this->execute();
        $id = $this->lastInsertId();
        $this->endTransaction();

        return $id;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        if (!empty($this->getId())) {
            $query = 'SELECT
                        id,
                        methodid,
                        consumeraccountid,
                        supplieraccountid,
                        timein,
                        timeout,
                        timems,
                        httpcode,
                        request,
                        response,
                        src,
						trg,
						engineid
					FROM
						requestlog
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        $query = 'SELECT
                    id,
                    methodid,
                    consumeraccountid,
                    supplieraccountid,
                    timein,
                    timeout,
                    timems,
                    httpcode,
                    request,
                    response,
                    src,
					trg,
					engineid
                FROM
                    requestlog';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        return $result;
    }

    /**
     * @param array $details
     * @return bool
     */
    public function set($details)
    {
        if (!empty($details) && is_array($details)) {
            $this->setId($details['id'] ? $details['id'] : null);
            $this->setMethodId($details['methodid'] ? $details['methodid'] : null);
            $this->setConsumerAccountId($details['consumeraccountid'] ? $details['consumeraccountid'] : null);
            $this->setSupplierAccountId($details['supplieraccountid'] ? $details['supplieraccountid'] : null);
            $this->setTimeIn($details['timein'] ? $details['timein'] : null);
            $this->setTimeOut($details['timeout'] ? $details['timeout'] : null);
            $this->setTimeMs($details['timems'] ? $details['timems'] : null);
            $this->setHttpCode($details['httpcode'] ? $details['httpcode'] : null);
            $this->setRequest($details['request'] ? $details['request'] : null);
            $this->setResponse($details['response'] ? $details['response'] : null);
            $this->setSrc($details['src'] ? $details['src'] : null);
            $this->setTrg($details['trg'] ? $details['trg'] : null);
            $this->setEngineId($details['engineid'] ? $details['engineid'] : null);

            return true;
        }

        return false;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getMethodId()
    {
        return $this->methodId;
    }

    /**
     * @param null $methodId
     */
    public function setMethodId($methodId)
    {
        $this->methodId = $methodId;
    }

    /**
     * @return null
     */
    public function getConsumerAccountId()
    {
        return $this->consumerAccountId;
    }

    /**
     * @param null $consumerAccountId
     */
    public function setConsumerAccountId($consumerAccountId)
    {
        $this->consumerAccountId = $consumerAccountId;
    }

    /**
     * @return null
     */
    public function getSupplierAccountId()
    {
        return $this->supplierAccountId;
    }

    /**
     * @param null $supplierAccountId
     */
    public function setSupplierAccountId($supplierAccountId)
    {
        $this->supplierAccountId = $supplierAccountId;
    }

    /**
     * @return null
     */
    public function getTimeIn()
    {
        return $this->timeIn;
    }

    /**
     * @param null $timeIn
     */
    public function setTimeIn($timeIn)
    {
        $this->timeIn = $timeIn;
    }

    /**
     * @return null
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * @param null $timeOut
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
    }

    /**
     * @return null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param null $request
     */
    public function setRequest($request)
    {
        if (is_array($request)) {
            $request = json_encode($request);
        }

        $this->request = $request;
    }

    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param null $response
     */
    public function setResponse($response)
    {
        if (is_array($response)) {
            $response = json_encode($response);
        }

        $this->response = $response;
    }

    /**
     * @return null
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param null $httpCode
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @return null
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param null $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return null
     */
    public function getTrg()
    {
        return $this->trg;
    }

    /**
     * @param null $trg
     */
    public function setTrg($trg)
    {
        $this->trg = $trg;
    }

    /**
     * @return null
     */
    public function getEngineId()
    {
        return $this->engineId;
    }

    /**
     * @param null $engineId
     */
    public function setEngineId($engineId)
    {
        $this->engineId = $engineId;
    }

    /**
     * @return null
     */
    public function getTimeMs()
    {
        return $this->timeMs;
    }

    /**
     * @param null $timeMs
     */
    public function setTimeMs($timeMs)
    {
        $this->timeMs = $timeMs;
    }
}
