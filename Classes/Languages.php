<?php

/**
 * Class Languages
 * @author Marek Mazur
 */
class Languages extends Database
{
    /**
     * @var null|int
     */
    private $id = null;

    /**
     * @var null|string
     */
    private $code = null;

    /**
     * @var null|string
     */
    private $name = null;

    /**
     * Default language
     */
    const LANGUAGE_EN = 'en';

    /**
     * Languages constructor.
     * @param null $id
     */
    public function __construct($id = null, $code = null)
    {
        $this->setId($id);
        $this->setCode($code);
        $this->set($this->get());
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getName())) {
            $query = 'INSERT INTO
							languages(
							    code,
								name
						)
						VALUES (
                            :code,
                            :name
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
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
        if (!empty($this->getId() || !empty($this->getCode()))) {
            $query = 'SELECT
                        id,
                        code,
						name
					FROM
						languages
					WHERE 
					    id = :id
                    OR  
                        code = :code';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
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
                    code,
                    name
                FROM
                    languages
                ORDER BY 
                    name';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $languages = [];

            foreach ($result as $row) {
                $languages[$row['code']] = $row;
            }

            return $languages;
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
							languages
						SET
						    code  = :code, 
                            name  = :name  
						WHERE
							id	  = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
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
            $this->setCode($details['code'] ? $details['code'] : null);
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param null $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
