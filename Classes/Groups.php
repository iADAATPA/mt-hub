<?php

/**
 * Class Groups
 * @package Classes
 */
class Groups extends Database
{
    private $id = null;
    private $name = null;

    const GROUP_SUPPLIER = 1;
    const GROUP_CONSUMER = 2;
    const GROUP_ADMINISTRATOR = 3;

    /**
     * Groups constructor.
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
     * @param $groupId
     * @return string
     */
    public static function getGroupName ($groupId)
    {
        switch ($groupId) {
            case Groups::GROUP_ADMINISTRATOR:
                return "Admin";

            case self::GROUP_SUPPLIER:
                return "Supplier";

            default:
                return "Consumer";
        }
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getName())) {
            $query = 'INSERT INTO
							groups(
								name
						)
						VALUES (
                            :name
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
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
						name
					FROM
						groups
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
                    name
                FROM
                    groups
                ORDER BY
                    name';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $groups = [];

            foreach ($result as $row) {
                $groups[$row['id']] = $row;
            }

            return $groups;
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
							groups
						SET
							name		= :name
						WHERE
							id			= :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
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
            $this->setName($details['name'] ? $details['name'] : null);

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
}