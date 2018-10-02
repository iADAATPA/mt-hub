<?php

/**
 * Class Statistics
 */
abstract class Statistics extends Database
{
    private $id = null;
    private $consumerAccountId = null;
    private $supplierAccountId = null;
    private $methodId = null;
    private $engineId = null;
    private $requestCount = null;
    private $wordCount = null;
    private $time = null;
    private $table = null;

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getTable())) {
            $query = 'INSERT INTO
							' . $this->getTable() . '(
								consumeraccountid,
								supplieraccountid,
								methodid,
								engineid,
								requestcount,
								wordcount,
								time
						)
						VALUES (
                            :consumeraccountid,
                            :supplieraccountid,
                            :methodid,
                            :engineid,
                            :requestcount,
                            :wordcount,
                            :time
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            $this->bindValue(':requestcount', $this->getRequestCount(), PDO::PARAM_INT);
            $this->bindValue(':wordcount', $this->getWordCount(), PDO::PARAM_INT);
            $this->bindValue(':time', $this->getTime(), PDO::PARAM_STR);
            $result = $this->execute();

            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        if (!empty($this->getTable() && !empty($this->getId()))) {
            $query = 'SELECT
                        id,
                        consumeraccountid,
                        supplieraccountid,
                        methodid,
                        engineid,
                        requestcount,
                        wordcount,
                        time
					FROM
						' . $this->getTable() . '
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
                    consumeraccountid,
                    supplieraccountid,
                    methodid,
                    engineid,
                    requestcount,
                    wordcount,
                    time
                FROM
                    ' . $this->getTable();

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $records = [];

            foreach ($result as $row) {
                $records[$row['id']] = $row;
            }

            return $records;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId() && !empty($this->getTable())) {
            $query = 'UPDATE 
							' . $this->getTable() . ' 
						SET
                            consumeraccountid   = :consumeraccountid,
                            supplieraccountid   = :supplieraccountid,
                            methodid            = :methodid,
                            engineid            = :engineid,
                            requestcount        = :requestcount,
                            wordcount           = :wordcount,
                            time                = :time
						WHERE
							id			      = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            $this->bindValue(':requestcount', $this->getRequestCount(), PDO::PARAM_INT);
            $this->bindValue(':wordcount', $this->getWordCount(), PDO::PARAM_INT);
            $this->bindValue(':time', $this->getTime(), PDO::PARAM_STR);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    public function delete()
    {
        if (!empty($this->getId() && !empty($this->getTable()))) {
            $query = 'DELETE FROM
						' . $this->getTable() . '
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @param array $details
     * @return bool
     */
    public function set($details)
    {
        if (!empty($details) && is_array($details)) {
            $this->setId($details['id'] ? $details['id'] : null);
            $this->setConsumerAccountId($details['consumeraccountid'] ? $details['consumeraccountid'] : null);
            $this->setSupplierAccountId($details['supplieraccountid'] ? $details['supplieraccountid'] : null);
            $this->setMethodId($details['methodid'] ? $details['methodid'] : null);
            $this->setEngineId($details['engineid'] ? $details['engineid'] : null);
            $this->setRequestCount($details['requestcount'] ? $details['requestcount'] : null);
            $this->setWordCount($details['wordcount'] ? $details['wordcount'] : null);
            $this->setTime($details['time'] ? $details['time'] : null);

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
    public function getWordCount()
    {
        return $this->wordCount;
    }

    /**
     * @param null $wordCount
     */
    public function setWordCount($wordCount)
    {
        $this->wordCount = $wordCount;
    }

    /**
     * @return null
     */
    public function getRequestCount()
    {
        return $this->requestCount;
    }

    /**
     * @param null $requestCount
     */
    public function setRequestCount($requestCount)
    {
        $this->requestCount = $requestCount;
    }

    /**
     * @return null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param null $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return null
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param null $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
}
