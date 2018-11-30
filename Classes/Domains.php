<?php

/**
 * Class Domainss
 */
class Domains extends Database
{
    private $id = null;
    private $accountId = null;
    private $name = null;
    private $src = null;

    const DOMAIN_GENERAL = 3;

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
        if (!empty($this->getName())) {
            $query = 'INSERT INTO
							domains(
							    accountid,
								name,
								src
						)
						VALUES (
						    :accountid,
                            :name,
                            :src
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':src', $this->getSrc(), PDO::PARAM_STR);
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
						name,
						src
					FROM
						domains
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
                    accountid,
                    name,
                    src
                FROM
                    domains';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $domains = [];

            foreach ($result as $row) {
                $domains[$row['id']] = $row;
            }

            return $domains;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAccountDomains()
    {
        if ($this->getAccountId()) {
            $query = 'SELECT
                    id,
                    accountid,
                    name,
                    src
                FROM
                    domains
                WHERE
                    accountid = :accountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            if ($result) {
                $domains = [];

                foreach ($result as $row) {
                    $domains[$row['id']] = $row;
                }

                return $domains;
            }
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
							domains
						SET
						    accountid = :accountid,
							name      = :name,
							src       = :src
						WHERE
							id	      = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
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
            $this->setAccountId($details['accountid'] ? $details['accountid'] : null);
            $this->setName($details['name'] ? $details['name'] : null);
            $this->setSrc($details['src'] ? $details['src'] : null);

            return true;
        }

        return false;
    }

    public function delete()
    {
        if (!empty($this->getId())) {
            $query = 'DELETE FROM
						domains
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

    public function getDomainByNameAndSource($name, $source)
    {
        if (!empty($name) && !empty($source)) {
            $query = 'SELECT
                        id,
                        name
					FROM
						domains
					WHERE 
					    (name      = :name 
                    OR 
                        id         = :id)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':name', $name, PDO::PARAM_STR);
            $this->bindValue(':id', $name, PDO::PARAM_STR);
            //$this->bindValue(':src', $source, PDO::PARAM_STR);

            $result = $this->result();
            $this->endTransaction();

            if (!$result) {
                $query = 'SELECT
                    id,
                    name
                FROM
                    domains
                WHERE 
                    name      = :name';

                $this->startTransaction();
                $this->query($query);
                $this->bindValue(':name', $name, PDO::PARAM_STR);

                $result = $this->result();
                $this->endTransaction();

            }

            return $result;
        }

        return null;
    }

    public function validateDomain($name, $src, $accountId)
    {
        if (!empty($name) && !empty($src)) {
            $query = 'SELECT
                        id
					FROM
						domains
					WHERE 
					    name      = :name 
                    AND 
                        src       = :src
                    AND   
                        accountid = :accountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':name', $name, PDO::PARAM_STR);
            $this->bindValue(':src', $src, PDO::PARAM_STR);
            $this->bindValue(':accountid', $accountId, PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            if ($result) {
                return false;
            }
        }

        return true;
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
}
