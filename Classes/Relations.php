<?php

/**
 * Class DomainsData
 */
class Relations extends Database
{
    private $id = null;
    private $consumerAccountId = null;
    private $supplierAccountId = null;
    private $supplierApiToken = null;
    private $description = null;
    private $added = null;
    private $apiToken = null;
    private $userName = null;
    private $password = null;
    private $token = null;

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
        if (!empty($this->getConsumerAccountId())) {
            $query = 'INSERT INTO
							relations (
								consumeraccountid,
								supplieraccountid,
								supplierapitoken,
								description,
								added,
								apitoken,
								username,
								password,
								token
						)
						VALUES (
							:consumeraccountid,
                            :supplieraccountid,
                            :supplierapitoken,
                            :description,
                            NOW(),
                            :apitoken,
                            :username,
                            :password,
                            :token
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':supplierapitoken', $this->getSupplierApiToken(), PDO::PARAM_STR);
            $this->bindValue(':description', $this->getDescription(), PDO::PARAM_INT);
            $this->bindValue(':apitoken', $this->getApiToken(), PDO::PARAM_STR);
            $this->bindValue(':username', $this->getUserName(), PDO::PARAM_STR);
            $this->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);
            $this->bindValue(':token', $this->getToken(), PDO::PARAM_STR);
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
						consumeraccountid,
                        supplieraccountid,
                        supplierapitoken,
                        description,
                        added,
                        apitoken,
                        username,
                        password,
                        token
					FROM
						relations
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
    public function getSupplierConsumerRelation()
    {
        if (!empty($this->getConsumerAccountId()) && !empty($this->getSupplierAccountId())) {
            $query = 'SELECT
                        id,
						consumeraccountid,
                        supplieraccountid,
                        supplierapitoken,
                        description,
                        added,
                        apitoken,
                        username,
                        password,
                        token
					FROM
						relations
					WHERE 
					    supplieraccountid   = :supplieraccountid
                    AND   
                        consumeraccountid   = :consumeraccountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
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
                    supplierapitoken,
                    description,
                    added,
                    apitoken,
                    username,
                    password,
                    token
                FROM
                    relations';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $relations = [];

            foreach ($result as $row) {
                $relations[$row['id']] = $row;
            }

            return $relations;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getSuppliersAssignedToConsumer()
    {
        $query = 'SELECT
                    supplieraccountid
                FROM
                    relations';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $supplierIds = null;

            foreach ($result as $row) {
                $supplierIds[] = $row['supplieraccountid'];
            }

            return $supplierIds;
        }

        return $result;
    }

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId()) {
            $query = 'UPDATE
							relations
						SET
							consumeraccountid	= :consumeraccountid,
							supplieraccountid	= :supplieraccountid,
							supplierapitoken	= :supplierapitoken,
			                description		    = :description,
			                apitoken            = :apitoken,
                            username            = :username,
                            password            = :password,
                            token               = :token
						WHERE
							id			        = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_STR);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':supplierapitoken', $this->getSupplierApiToken(), PDO::PARAM_STR);
            $this->bindValue(':description', $this->getDescription(), PDO::PARAM_STR);
            $this->bindValue(':apitoken', $this->getApiToken(), PDO::PARAM_STR);
            $this->bindValue(':username', $this->getUserName(), PDO::PARAM_STR);
            $this->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);
            $this->bindValue(':token', $this->getToken(), PDO::PARAM_STR);
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
                            relations
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
            $this->setConsumerAccountId($details['consumeraccountid'] ? $details['consumeraccountid'] : null);
            $this->setSupplierAccountId($details['supplieraccountid'] ? $details['supplieraccountid'] : null);
            $this->setSupplierApiToken($details['supplierapitoken'] ? $details['supplierapitoken'] : null);
            $this->setDescription($details['description'] ? $details['description'] : null);
            $this->setAdded($details['added'] ? $details['added'] : null);
            $this->setApiToken($details['apitoken'] ? $details['apitoken'] : null);
            $this->setUserName($details['username'] ? $details['username'] : null);
            $this->setPassword($details['password'] ? $details['password'] : null);
            $this->setToken($details['token'] ? $details['token'] : null);

            return true;
        }

        return false;
    }

    /**
     * @return array|null
     */
    public function getConsumerSuppliers()
    {
        $query = 'SELECT
                    relations.id,
                    relations.consumeraccountid,
                    relations.supplieraccountid,
                    accounts.name,
                    relations.supplierapitoken,
                    relations.apitoken,
                    relations.token,
                    relations.username,
                    relations.password,
                    relations.description,
                    relations.added
                FROM
                    relations
                LEFT JOIN 
                    accounts ON relations.supplieraccountid = accounts.id
                WHERE 
                    relations.consumeraccountid = :consumeraccountid';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue('consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $relations = [];

            foreach ($result as $row) {
                $relations[$row['supplieraccountid']] = $row;
            }

            return $relations;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getSupplierConsumers()
    {
        $query = 'SELECT
                    relations.id,
                    relations.consumeraccountid,
                    relations.supplieraccountid,
                    relations.supplierapitoken,
                    relations.apitoken,
                    relations.token,
                    relations.description,
                    relations.added,
                    accounts.name
                FROM
                    relations
                INNER JOIN accounts ON accounts.id = relations.consumeraccountid
                WHERE 
                    relations.supplieraccountid = :supplieraccountid';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue('supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $consumers = [];

            foreach ($result as $row) {
                $consumers[$row['consumeraccountid']] = $row;
            }

            return $consumers;
        }

        return null;
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
    public function getSupplierApiToken()
    {
        return $this->supplierApiToken;
    }

    /**
     * @param null $supplierApiToken
     */
    public function setSupplierApiToken($supplierApiToken)
    {
        $this->supplierApiToken = $supplierApiToken;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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

    /**
     * @return null
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param null $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return null
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param null $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
