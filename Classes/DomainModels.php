<?php

/**
 * Class DomainModels
 */
class DomainModels extends Database
{
    /**
     * @var null|int
     */
    private $id = null;

    /**
     * @var null|int
     */
    private $accountId = null;

    /**
     * @var null|object
     */
    private $model = null;

    /**
     * @var null|string
     */
    private $created = null;

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
        if (!empty($this->getAccountId())) {
            $query = 'INSERT INTO
							domainmodels (
								accountid,
								model,
								created
						)
						VALUES (
                            :accountid,
                            :model,
                            NOW()
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':model', $this->getmodel(), PDO::PARAM_STR);
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
                        accountid,
                        model,
                        created
					FROM
						domainmodels
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
     * @return mixed|null
     */
    public function getAccountModel()
    {
        if (!empty($this->getAccountId())) {
            $query = 'SELECT
                        model
					FROM
						domainmodels
					WHERE 
					    accountid = :accountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            if ($result) {
                return $result["model"];
            }
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
                    accountid,
                    model,
                    created
                FROM
                    domainmodels';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $domainsData = [];

            foreach ($result as $row) {
                $domainsData[$row['id']] = $row;
            }

            return $domainsData;
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
							domainmodels
						SET
							accountid	= :accountid,
							model	    = :model,
			                created		= :created,
						WHERE
							id			= :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':model', $this->getModel(), PDO::PARAM_STR);
            $this->bindValue(':created', $this->getCreated(), PDO::PARAM_INT);
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
                            domainmodels
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

    public function deleteAccountModelsExLastOne()
    {
        if (!empty($this->getAccountId())) {
            // First check if there are any models and get an id of the latest one
            $query = 'SELECT 
                            MAX(id) AS maxid
                        FROM 
                            domainmodels 
                        WHERE 
                            accountid  = :accountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            if ($result) {
                $query = 'DELETE FROM
                            domainmodels
                        WHERE
                            accountid  = :accountid
                        AND 
                            id != :id';

                $this->startTransaction();
                $this->query($query);
                $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
                $this->bindValue(':id', $result['maxid'], PDO::PARAM_INT);
                $result = $this->execute();
                $this->endTransaction();
            }

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
            $this->setAccountId($details['accountid'] ? $details['accountid'] : null);
            $this->setModel($details['model'] ? $details['model'] : null);
            $this->setCreated($details['created'] ? $details['created'] : null);

            return true;
        }

        return false;
    }

    public function compileDomainModels($supplierAccountId)
    {
        // Get the data for consumers. We start from getting a list of consumer suppliers
        $relations = new Relations();
        $relations->setSupplierAccountId($supplierAccountId);
        $supplierConsumers = $relations->getSupplierConsumers();
        $domainData = new DomainData();
        $response = null;

        // For evey consumer that is connected to the suppier we need to rebuild the domains model
        if ($supplierConsumers && is_array($supplierConsumers)) {
            foreach ($supplierConsumers as $consumerAccountId => $consumerDetails) {
                // for every consumer we need get all their suppliers and their data
                $relations->setConsumerAccountId($consumerAccountId);
                $consumerSuppliers = $relations->getConsumerSuppliers();
                $consumerSuppliers = is_array($consumerSuppliers) ? array_keys($consumerSuppliers) : null;

                $data = $domainData->getSuppliersDomainData($consumerSuppliers);

                $classifierData = null;
                if ($data && is_array($data)) {
                    foreach ($data as $dataDetails) {
                        $classifierData[$dataDetails['domainid']] = empty($classifierData[$dataDetails['domainid']]) ? $dataDetails['segments'] : $classifierData[$dataDetails['domainid']] . ' ' . $dataDetails['segments'];
                    }
                }

                // Pass the data to Classifier
                $trainer = new Trainer();
                $trainer->setData($classifierData);
                $response = $trainer->compile();

                if ($response || count($data) === 0) {
                    $domainModels = new DomainModels();
                    $domainModels->setAccountId($consumerAccountId);
                    $domainModels->setModel($response);
                    $response = $domainModels->insert();
                    $domainModels->deleteAccountModelsExLastOne();

                    if ($response) {
                        // Log the event
                        Log::save(Log::DOMAINDATA_UPDATED, $response);
                    }
                }
            }
        }

        return $response;
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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param null $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param null $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
}
