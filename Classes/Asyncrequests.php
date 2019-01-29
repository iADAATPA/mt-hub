<?php

/**
 * Class AsyncRequests
 * @author Marek Mazur
 */
class AsyncRequests extends Database
{
    /**
     * @var null|string
     */
    private $uuId = null;

    /**
     * @var null|int
     */
    private $accountId = null;

    /**
     * @var null|int
     */
    private $jobId = null;

    /**
     * @var null|int
     */
    private $engineId = null;

    /**
     * @var null|string
     */
    private $engineName = null;

    /**
     * @var null|string
     */
    private $timeReceived = null;

    /**
     * @var null|string
     */
    private $timeTranslated = null;

    /**
     * @var null|string
     */
    private $timePulled = null;

    /**
     * @var null|int
     */
    private $status = null;

    /**
     * @var null|string
     */
    private $source = null;

    /**
     * @var null|string
     */
    private $target = null;

    /**
     * AsyncRequests constructor.
     * @param string $uuId
     */
    public function __construct($uuId = null)
    {
        if ($uuId) {
            $this->setUuId($uuId);
            $details = $this->get();
            $this->setDetails($details);
        }
    }

    /**
     * Insert a new record. THe uuid and at least accountid and an engineName must be set
     *
     * @return mixed
     */
    public function insert()
    {
        // Check if type and account set
        if ($this->getUuId() && $this->getAccountId() && $this->getEngineName()) {
            $query = 'INSERT INTO
							asyncrequests (
							    uuid,
								accountid,
								jobid,
								engineid,
								enginename,
								timereceived,
								timetranslated,
								timepulled,
								status,
								source,
								target
							)
						VALUES (							    
						        :uuid,
								:accountid,
								:jobid,
								:engineid,
								:enginename,
								:timereceived,
								:timetranslated,
								:timepulled,
								:status,
								:source,
								:target
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':uuid', $this->getUuId(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':jobid', $this->getJobId(), PDO::PARAM_INT);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            $this->bindValue(':enginename', $this->getEngineName(), PDO::PARAM_STR);
            $this->bindValue(':timereceived', $this->getTimeReceived(), PDO::PARAM_INT);
            $this->bindValue(':timetranslated', $this->getTimeTranslated(), PDO::PARAM_INT);
            $this->bindValue(':timepulled', $this->getTimePulled(), PDO::PARAM_INT);
            $this->bindValue(':status', $this->getStatus(), PDO::PARAM_STR);
            $this->bindValue(':source', $this->getSource(), PDO::PARAM_STR);
            $this->bindValue(':target', $this->getTarget(), PDO::PARAM_STR);
            $response = $this->execute();
            $this->endTransaction();

            return $response;
        }
    }

    /**
     * Update async record
     * The uuid and account id must be set
     * @return mixed
     */
    public function update()
    {
        // Check if type and account set
        if ($this->getUuId() && $this->getAccountId()) {
            $query = 'UPDATE
							asyncrequests
						SET
                            accountid        = :accountid,
                            jobid            = :jobid,
                            engineid         = :engineid,
                            enginename       = :enginename,
                            timereceived     = :timereceived,
                            timetranslated   = :timetranslated,
                            timepulled       = :timepulled,    
                            status           = :status,
                            source           = :source,
                            target           = :target
						WHERE
							uuid			  = :uuid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':uuid', $this->getUuId(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':jobid', $this->getJobId(), PDO::PARAM_INT);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            $this->bindValue(':enginename', $this->getEngineName(), PDO::PARAM_STR);
            $this->bindValue(':timereceived', $this->getTimeReceived(), PDO::PARAM_INT);
            $this->bindValue(':timetranslated', $this->getTimeTranslated(), PDO::PARAM_INT);
            $this->bindValue(':timepulled', $this->getTimePulled(), PDO::PARAM_INT);
            $this->bindValue(':status', $this->getStatus(), PDO::PARAM_STR);
            $this->bindValue(':source', $this->getSource(), PDO::PARAM_STR);
            $this->bindValue(':target', $this->getTarget(), PDO::PARAM_STR);
            $response = $this->execute();
            $this->endTransaction();

            return $response;
        } else {
            return false;
        }
    }

    /**
     * Set asyn request details
     *
     * @param $details
     */
    public function setDetails($details)
    {
        $this->setUuId(isset($details['uuid']) ? $details['uuid'] : null);
        $this->setAccountId(isset($details['accountid']) ? $details['accountid'] : null);
        $this->setJobId(isset($details['jobid']) ? $details['jobid'] : null);
        $this->setEngineId(isset($details['engineid']) ? $details['engineid'] : null);
        $this->setEngineName(isset($details['enginename']) ? $details['enginename'] : null);
        $this->setTimeReceived(isset($details['timereceived']) ? $details['timereceived'] : null);
        $this->setTimeTranslated(isset($details['timetranslated']) ? $details['timetranslated'] : null);
        $this->setTimePulled(isset($details['timepulled']) ? $details['timepulled'] : null);
        $this->setStatus(isset($details['status']) ? $details['status'] : null);
        $this->setSource(isset($details['source']) ? $details['source'] : null);
        $this->setTarget(isset($details['target']) ? $details['target'] : null);
    }

    /**
     * Get async request
     * The uuid must be set
     *
     * @return mixed|null
     */
    public function get()
    {
        if ($this->getUuId()) {
            $query = 'SELECT
							uuid,
                            accountid,
                            jobid,
                            engineid,
                            enginename,
                            timereceived,
                            timetranslated,
                            timepulled,
                            status,
                            source,
                            target
						FROM
							asyncrequests
						WHERE
							uuid	= :uuid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':uuid', $this->getUuId(), PDO::PARAM_STR);
            $result = $this->single();
            $this->endTransaction();

            return $result;
        } else {
            return null;
        }
    }

    /**
     * Delete asyn request
     * The uuid must be set
     *
     * @return bool|mixed
     */
    public function delete()
    {
        if ($this->getUuId()) {
            $query = 'DELETE
							*
						FROM
							asyncrequests
						WHERE
							uuid	= :uuid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':uuid', $this->getUuId(), PDO::PARAM_STR);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        } else {
            return false;
        }
    }

    /**
     * @return null|string
     */
    public function getUuId()
    {
        return $this->uuId;
    }

    /**
     * @param string $uuId
     */
    public function setUuId($uuId)
    {
        $this->uuId = $uuId;
    }

    /**
     * @return null|int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return null|int
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * @param int $jobId
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * @return null|int
     */
    public function getEngineId()
    {
        return $this->engineId;
    }

    /**
     * @param int $engineId
     */
    public function setEngineId($engineId)
    {
        $this->engineId = $engineId;
    }

    /**
     * @return null|string
     */
    public function getEngineName()
    {
        return $this->engineName;
    }

    /**
     * @param string $engineName
     */
    public function setEngineName($engineName)
    {
        $this->engineName = $engineName;
    }

    /**
     * @return int|null
     */
    public function getTimeReceived()
    {
        return $this->timeReceived;
    }

    /**
     * @param int $timeReceived
     */
    public function setTimeReceived($timeReceived)
    {
        $this->timeReceived = $timeReceived;
    }

    /**
     * @return null|int
     */
    public function getTimeTranslated()
    {
        return $this->timeTranslated;
    }

    /**
     * @param int $timeTranslated
     */
    public function setTimeTranslated($timeTranslated)
    {
        $this->timeTranslated = $timeTranslated;
    }

    /**
     * @return null|int
     */
    public function getTimePulled()
    {
        return $this->timePulled;
    }

    /**
     * @param int $timePulled
     */
    public function setTimePulled($timePulled)
    {
        $this->timePulled = $timePulled;
    }

    /**
     * @return null|string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param null|string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return null|string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param null|string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return null|string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param null|string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }
}
