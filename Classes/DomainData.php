<?php

/**
 * Class DomainData
 */
class DomainData extends Database
{
    private $id = null;
    private $domainId = null;
    private $accountId = null;
    private $segments = null;
    private $added = null;

    /**
     * Accounts constructor.
     * @param null|int $id
     */
    public function __construct($id = null)
    {
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
        if (!empty($this->getDomainId())) {
            $query = 'INSERT INTO
							domaindata (
								domainid,
								accountid,
								segments,
								added
						)
						VALUES (
							:domainid,
                            :accountid,
                            :segments,
                            NOW()
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':segments', $this->getSegments(), PDO::PARAM_STR);
            $this->execute();
            $id = $this->lastInsertId();
            $this->endTransaction();

            return $id;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        if (!empty($this->getId())) {
            $query = 'SELECT
                        id,
						domainid,
                        accountid,
                        segments,
                        added
					FROM
						domaindata
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
                    domainid,
                    accountid,
                    segments,
                    added
                FROM
                    domaindata';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $domainData = [];

            foreach ($result as $row) {
                $domainData[$row['id']] = $row;
            }

            return $domainData;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId()) {
            $query = 'UPDATE
							domaindata
						SET
							domainid	= :domainid,
							accountid	= :accountid,
							segments	= :segments,
			                added		= :added,
						WHERE
							id			= :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':segments', $this->getSegments(), PDO::PARAM_STR);
            $this->bindValue(':added', $this->getAdded(), PDO::PARAM_INT);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    public function deleteDomainDataExLastOne()
    {
        if (!empty($this->getAccountId())) {
            // First check if there are any models and get an id of the latest one
            $query = 'SELECT 
                            MAX(id) AS maxid
                        FROM 
                            domaindata 
                        WHERE 
                            accountid  = :accountid
                        AND   
                            domainid   = :domainid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_STR);
            $result = $this->result();
            $this->endTransaction();

            if ($result) {
                $query = 'DELETE FROM
                            domaindata
                        WHERE
                            accountid  = :accountid
                        AND 
                            domainid   = :domainid
                        AND 
                            id != :id';

                $this->startTransaction();
                $this->query($query);
                $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
                $this->bindValue(':id', $result['maxid'], PDO::PARAM_INT);
                $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_STR);
                $result = $this->execute();
                $this->endTransaction();
            }

            return $result;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getSuppliersDomainData($suppliersArray)
    {
        $suppliers = is_array($suppliersArray) ? implode(',', $suppliersArray) : null;

        if ($suppliers) {
            $query = 'SELECT
                    id,
                    domainid,
                    accountid,
                    segments,
                    added
                FROM
                    domaindata
                WHERE
                    accountid IN (' . $suppliers . ')
                ORDER BY
                    domainid';


            $this->startTransaction();
            $this->query($query);
            $result = $this->resultSet();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return bool|null
     */
    public function deleteDomainData()
    {
        if (!empty($this->getAccountId() && !empty($this->getDomainId()))) {
            $query = 'DELETE FROM
                            domaindata
                        WHERE
                            accountid = :accountid
                        AND 
                            domainid  = :domainid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    public function delete()
    {
        if (!empty($this->getId())) {
            $query = 'DELETE FROM
                            domaindata
                        WHERE
                            id  = :id';

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
            $this->setDomainId($details['domainid'] ? $details['domainid'] : null);
            $this->setAccountId($details['accountid'] ? $details['accountid'] : null);
            $this->setSegments($details['segments'] ? $details['segments'] : null);
            $this->setAdded($details['added'] ? $details['added'] : null);

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
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param null $domainId
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
    }

    /**
     * @return null
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param null $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return null
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param null $segment
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }

    /**
     * @return null
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param null $added
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }
}
