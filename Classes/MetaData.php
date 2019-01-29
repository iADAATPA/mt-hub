<?php

/**
 * Class MetaData
 * @author Marek Mazur
 */
class MetaData extends Database
{
    /**
     * @var null|int
     */
    private $id = null;

    /**
     * @var null|int
     */
    private $engineId = null;

    /**
     * @var null|string
     */
    private $variable = null;

    /**
     * @var null|string
     */
    private $value = null;

    /**
     * UrlConfig constructor.
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
        if ($this->getVariable() && $this->getEngineId() && $this->getValue()) {
            $query = 'INSERT INTO
							metadata(
							    variable,
								value,
								engineid
						)
						VALUES (
							:variable,
                            :value,
                            :engineid
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':variable', $this->getVariable(), PDO::PARAM_STR);
            $this->bindValue(':value', $this->getValue(), PDO::PARAM_STR);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
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
						variable,
                        value,
                        engineid
					FROM
						metadata
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
        if ($this->getEngineId()) {
            $query = 'SELECT
                    id,
                    variable,
                    value,
                    engineid
                FROM
                    metadata
                WHERE
                    engineid = :engineid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            return $result;
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
							metadata
						SET
							variable  = :variable,
							value	  = :value,
							engineid  = :engineid
						WHERE
							id		  = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':variable', $this->getVariable(), PDO::PARAM_STR);
            $this->bindValue(':value', $this->getValue(), PDO::PARAM_STR);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_STR);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    public function deleteEngineMetaData()
    {
        if (!empty($this->getEngineId())) {
            $query = 'DELETE FROM
                            metadata
                        WHERE
                            engineid  = :engineid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
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
                            metadata
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
            $this->setVariable($details['variable'] ? $details['variable'] : null);
            $this->setValue($details['value'] ? $details['value'] : null);
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
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param null $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
