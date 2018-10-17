<?php

/**
 * Class Accounts
 * @package Classes
 */
class Accounts extends Database
{
    private $id = null;
    private $groupId = null;
    private $active = null;
    private $name = null;
    private $logo = null;
    private $adminId = null;
    private $created = null;
    private $expired = null;
    private $deleted = null;
    private $apiToken = null;
    private $token = null;
    private $activiaTmUserName = null;
    private $activiaTmPassword = null;
    private $activiaTm = null;
    private $cache = null;

    /**
     * Accounts constructor.
     * @param null|int $id
     */
    public function __construct($id = null, $token = null)
    {
        $this->setApiToken(trim($token));
        $this->setId(trim($id));
        $this->set($this->get());
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getName())) {
            $query = 'INSERT INTO
							accounts(
								groupid,
								active,
								name,
								logo,
								adminid,
								created,
							    expired,
			                    deleted,
			                    apitoken,
			                    cache,
			                    activiatm,
			                    activiatmusername,
			                    activiatmpassword,
			                    token
						)
						VALUES (
							:groupid,
                            :active,
                            :name,
                            :logo,
                            :adminid,
                            NOW(),
                            :expired,
                            :deleted,
                            :apitoken,
                            :cache,
                            :activiatm,
                            :activiatmusername,
                            :activiatmpassword,
                            :token
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':groupid', $this->getGroupId(), PDO::PARAM_INT);
            $this->bindValue(':active', $this->getActive(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':logo', $this->getLogo(), PDO::PARAM_STR);
            $this->bindValue(':adminid', $this->getAdminId(), PDO::PARAM_INT);
            $this->bindValue(':expired', $this->getExpired(), PDO::PARAM_INT);
            $this->bindValue(':deleted', $this->getDeleted(), PDO::PARAM_INT);
            $this->bindValue(':apitoken', $this->getApiToken(), PDO::PARAM_STR);
            $this->bindValue(':cache', $this->getCache(), PDO::PARAM_INT);
            $this->bindValue(':activiatm', $this->getActiviaTm(), PDO::PARAM_INT);
            $this->bindValue(':activiatmusername', $this->getActiviaTmUserName(), PDO::PARAM_STR);
            $this->bindValue(':activiatmpassword', $this->getActiviaTmPassword(), PDO::PARAM_STR);
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
        if (!empty($this->getId() || !empty($this->getApiToken()))) {
            $query = 'SELECT
                        id,
						groupid,
                        active,
                        name,
                        logo,
                        adminid,
                        created,
                        expired,
                        deleted,
                        apitoken,
                        cache,
                        activiatm,
                        activiatmusername,
                        activiatmpassword,
                        token
					FROM
						accounts
					WHERE 
					    id = :id
                    OR 
                        apitoken = :apitoken';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':apitoken', $this->getApiToken(), PDO::PARAM_STR);
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
                    groupid,
                    active,
                    name,
                    logo,
                    adminid,
                    created,
                    expired,
                    deleted,
                    apitoken,
                    cache,
                    activiatm,
                    activiatmusername,
                    activiatmpassword,
                    token
                FROM
                    accounts';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $accounts = [];

            foreach ($result as $row) {
                $accounts[$row['id']] = $row;
            }

            return $accounts;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAllConsumers()
    {
        $query = 'SELECT
                    accounts.id,
                    accounts.groupid,
                    accounts.active,
                    accounts.name,
                    accounts.logo,
                    accounts.adminid,
                    accounts.created,
                    accounts.expired,
                    accounts.deleted,
                    accounts.apitoken,
                    users.email
                FROM
                    accounts
                LEFT JOIN users ON accounts.adminid = users.id
                WHERE
                    accounts.groupid = :groupid
                AND 
                    accounts.active = 1';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':groupid', Groups::GROUP_CONSUMER, PDO::PARAM_INT);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $accounts = [];

            foreach ($result as $row) {
                $accounts[$row['id']] = $row;
            }

            return $accounts;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAllSuppliers()
    {
        $query = 'SELECT
                    accounts.id,
                    accounts.groupid,
                    accounts.active,
                    accounts.name,
                    accounts.logo,
                    accounts.adminid,
                    accounts.created,
                    accounts.expired,
                    accounts.deleted,
                    accounts.apitoken,
                    users.email
                FROM
                    accounts
                LEFT JOIN users ON accounts.adminid = users.id
                WHERE
                    accounts.groupid = :groupid
                AND 
                    accounts.active = 1';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':groupid', Groups::GROUP_SUPPLIER, PDO::PARAM_INT);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $accounts = [];

            foreach ($result as $row) {
                $accounts[$row['id']] = $row;
            }

            return $accounts;
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
							accounts
						SET
							groupid		        = :groupid,
							active		        = :active,
							name		        = :name,
			                logo                = :logo,
							adminid	            = :adminid,	
							expired             = :expired,
			                deleted             = :deleted,
			                apitoken            = :apitoken,
			                cache               = :cache,
                            activiatm           = :activiatm,
                            activiatmusername   = :activiatmusername,
                            activiatmpassword   = :activiatmpassword,
                            token               = :token
						WHERE
							id			        = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':groupid', $this->getGroupId(), PDO::PARAM_STR);
            $this->bindValue(':active', $this->getActive(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':logo', $this->getLogo(), PDO::PARAM_STR);
            $this->bindValue(':adminid', $this->getAdminId(), PDO::PARAM_INT);
            $this->bindValue(':expired', $this->getExpired(), PDO::PARAM_STR);
            $this->bindValue(':deleted', $this->getDeleted(), PDO::PARAM_STR);
            $this->bindValue(':apitoken', $this->getApiToken(), PDO::PARAM_STR);
            $this->bindValue(':cache', $this->getCache(), PDO::PARAM_INT);
            $this->bindValue(':activiatm', $this->getActiviaTm(), PDO::PARAM_INT);
            $this->bindValue(':activiatmusername', $this->getActiviaTmUserName(), PDO::PARAM_STR);
            $this->bindValue(':activiatmpassword', $this->getActiviaTmPassword(), PDO::PARAM_STR);
            $this->bindValue(':token', $this->getToken(), PDO::PARAM_STR);
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
            $this->setGroupId($details['groupid'] ? $details['groupid'] : null);
            $this->setActive($details['active'] ? $details['active'] : null);
            $this->setName($details['name'] ? $details['name'] : null);
            $this->setLogo($details['logo'] ? $details['logo'] : null);
            $this->setAdminId($details['adminid'] ? $details['adminid'] : null);
            $this->setCreated($details['created'] ? $details['created'] : null);
            $this->setExpired($details['expired'] ? $details['expired'] : null);
            $this->setDeleted($details['deleted'] ? $details['deleted'] : null);
            $this->setApiToken($details['apitoken'] ? $details['apitoken'] : null);
            $this->setCache($details['cache'] ? $details['cache'] : null);
            $this->setActiviaTm($details['activiatm'] ? $details['activiatm'] : null);
            $this->setActiviaTmUserName($details['activiatmusername'] ? $details['activiatmusername'] : null);
            $this->setActiviaTmPassword($details['activiatmpassword'] ? $details['activiatmpassword'] : null);
            $this->setToken($details['token'] ? $details['token'] : null);

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
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param null $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return null
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param null $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param null $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return null
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param null $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
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
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * @param null $expired
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
    }

    /**
     * @return null
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param null $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return null
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param null $apitoken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
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

    /**
     * @return null
     */
    public function getActiviaTmUserName()
    {
        return $this->activiaTmUserName;
    }

    /**
     * @param null $activiaTmUserName
     */
    public function setActiviaTmUserName($activiaTmUserName)
    {
        $this->activiaTmUserName = $activiaTmUserName;
    }

    /**
     * @return null
     */
    public function getActiviaTmPassword()
    {
        return $this->activiaTmPassword;
    }

    /**
     * @param null $activiaTmPassword
     */
    public function setActiviaTmPassword($activiaTmPassword)
    {
        $this->activiaTmPassword = $activiaTmPassword;
    }

    /**
     * @return null
     */
    public function getActiviaTm()
    {
        return $this->activiaTm;
    }

    /**
     * @param null $activiaTm
     */
    public function setActiviaTm($activiaTm)
    {
        $this->activiaTm = $activiaTm;
    }

    /**
     * @return null
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param null $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }
}
