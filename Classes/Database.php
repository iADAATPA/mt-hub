<?php

/**
 * Class Database
 * @author Marek Mazur
 */
abstract class Database
{
    /**
     * @var null|object
     */
    private $db = null;

    /**
     * @var null|string
     */
    private $error = null;

    /**
     * @var null|object
     */
    private $stmt = null;

    /**
     * @var null|string
     */
    private $host = '';

    /**
     * @var null|string
     */
    private $name = '';

    /**
     * @var null|string
     */
    private $user = '';

    /**
     * @var null|string
     */
    private $pass = '';

    /**
     * Connect to db
     */
    public function connect()
    {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->name;
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 300,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // We are going to connect to the database. If we cannt connect the first time we try 4 more times
        $i = 0;
        do {
            try {
                $this->db = new PDO($dsn, $this->user, $this->pass, $options);
            } // Catch any errors
            catch (PDOException $e) {
                error_log($e);
                $this->error = $e->getMessage();
            }
            $i++;
        } while (($i < 5) && (!$this->db));
    }

    /**
     * Starts db transaction by connecting to db
     */
    public function startTransaction()
    {
        $this->connect();
    }

    /**
     *
     * Checks if the connection to the db is alife, if not reconnects to it
     */
    public function getConnection()
    {
        if ($this->db === null) {
            $this->connect();
        } else {
            if (is_object($this->db)) {
                if (!$this->db->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
                    $this->connect();
                }
            }
        }

        return $this->db;
    }

    /**
     * Disconnect from the db
     */
    public function disconnect()
    {
        $this->db = null;
    }

    /**
     * Ends db transaction
     */
    public function endTransaction()
    {
        $this->disconnect();
    }

    /**
     * Execute query
     */
    public function execute()
    {
        // Check if we are connected to db
        $this->getConnection();

        try {
            $res = $this->stmt->execute();
        } catch (Exception $e) {
            error_log($e);
            return false;
        }

        return $res;
    }

    /**
     * Prepare query
     *
     * @param string $query
     */
    public function query($query)
    {
        // Check if we are connected to db
        $this->getConnection();

        $this->stmt = $this->db->prepare($query);
    }

    /**
     * Execute query. Get single row
     */
    public function result()
    {
        $this->execute();

        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get last query row count
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    /**
     * Get latest id
     */
    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * Execute query. Get multiplle rows
     */
    public function resultSet()
    {
        $this->execute();

        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query. Get multiplle rows
     */
    public function resultSetNum()
    {
        $this->execute();

        return $this->stmt->fetchAll(PDO::FETCH_NUM);
    }

    /**
     * Bind value
     *
     * @param string $param
     * @param int|string $value
     * @param string $type
     */
    public function bindValue($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * @return false|string
     */
    public function getCurrentMySqlTime()
    {
        return date("Y-m-d H:i:s", time());
    }
}
