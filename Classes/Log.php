<?php

/**
 * Class Log
 *
 * @package Classes
 */
class Log extends Database
{
    private $id = null;
    private $accountId = null;
    private $userId = null;
    private $time = null;
    private $action = null;
    private $comment = null;
    private $differences = null;

    const ACCOUNT_SWITCHED = 10;
    const ACCOUNT_ADDED = 11;
    const ACCOUNT_DELETED = 12;
    const ACCOUNT_UPDATED = 13;
    const ACCOUNT_API_UPDATED = 14;

    const USER_LOGIN = 20;
    const USER_LOGOUT = 21;
    const USER_SET_PASSWORD = 22;
    const USER_ADDED = 23;
    const USER_DELETED = 24;
    const USER_UPDATED = 25;

    const URLCONFIG_ADDED = 30;
    const URLCONFIG_DELETED = 31;
    const URLCONFIG_UPDATED = 32;

    const METADATA_ADDED = 40;
    const METADATA_DELETED = 41;
    const METADATA_UPDATED = 42;

    const ENGINE_ADDED = 50;
    const ENGINE_COPIED = 51;
    const ENGINE_DELETED = 52;
    const ENGINE_UPDATED = 53;

    const DOMAIN_ADDED = 60;
    const DOMAIN_DELETED = 61;
    const DOMAIN_UPDATED = 62;

    const DOMAINDATA_ADDED = 70;
    const DOMAINDATA_DELETED = 71;
    const DOMAINDATA_UPDATED = 72;

    const CONSUMER_ADDED = 80;
    const CONSUMER_REMOVED = 81;
    const CONSUMER_UPDATED = 82;

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
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getAccountId() && !empty($this->getUserId())) && !empty($this->getAction())) {
            $query = 'INSERT INTO
							log(
								accountid,
								userid,
								action,
								comment,
								differences
						)
						VALUES (
                            :accountid,
                            :userid,
                            :action,
                            :comment,
                            :differences
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_STR);
            $this->bindValue(':userid', $this->getUserId(), PDO::PARAM_STR);
            $this->bindValue(':action', $this->getAction(), PDO::PARAM_INT);
            $this->bindValue(':comment', $this->getComment(), PDO::PARAM_STR);
            $this->bindValue(':differences', $this->getDifferences(), PDO::PARAM_STR);
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
                        time,
						accountid,
                        userid,
                        action,
                        comment,
                        differences
					FROM
						log
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
                    time,
                    accountid,
                    userid,
                    action,
                    comment,
                    differences
                FROM
                    log';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        return $result;
    }

    /**
     * @param array $details
     * @return bool
     */
    public function set($details)
    {
        if (!empty($details) && is_array($details)) {
            $this->setId($details['id'] ? $details['id'] : null);
            $this->setTime($details['time'] ? $details['time'] : null);
            $this->setAccountId($details['accountid'] ? $details['accountid'] : null);
            $this->setUserId($details['userid'] ? $details['userid'] : null);
            $this->setAction($details['action'] ? $details['action'] : null);
            $this->setComment($details['comment'] ? $details['comment'] : null);
            $this->setDifferences($details['differences'] ? $details['differences'] : null);

            return true;
        }

        return false;
    }

    /**
     * Log user action
     *
     * @param int $action
     * @param string $comment
     * @return mixed|null
     */
    public static function save($action, $comment = null, $differences = null)
    {
        $log = new Log();
        $accountId = Session::getAccountId() == Session::getLogAccountId() ? Session::getAccountId() : Session::getLogAccountId() . '=>' . Session::getAccountId();
        $userId = Session::getUserId() == Session::getLogUserId() ? Session::getUserId() : Session::getLogUserId() . '=>' . Session::getUserId();

        $log->setAccountId($accountId);
        $log->setUserId($userId);
        $log->setAction($action);
        $log->setComment($comment);
        $log->setDifferences(empty($differences) ? null : json_encode($differences));
        $result = $log->insert();

        return $result;
    }

    /**
     * @return array
     */
    public static function getActionDescriptionList()
    {
        $actionList = [
            Log::ACCOUNT_SWITCHED => "Switched account to #",
            Log::ACCOUNT_ADDED => "Created a new account #",
            Log::ACCOUNT_DELETED => "Account deleted",
            Log::ACCOUNT_UPDATED => "Account updated ",
            Log::ACCOUNT_API_UPDATED => "API token updated",
            Log::USER_LOGIN => "User logged in",
            Log::USER_LOGOUT => "User logged out",
            Log::USER_SET_PASSWORD => "User set password",
            Log::USER_ADDED => "Created a new user #",
            Log::USER_DELETED => "User deleted #",
            Log::USER_UPDATED => "User updated #",
            Log::URLCONFIG_ADDED => "URL Configuration added #",
            Log::URLCONFIG_DELETED => "URL Configuration deleted #",
            Log::URLCONFIG_UPDATED => "URL Configuration updated #",
            Log::METADATA_ADDED => "Metadata added #",
            Log::METADATA_DELETED => "Metadata deleted #",
            Log::METADATA_UPDATED => "Metadata updated #",
            Log::ENGINE_ADDED => "Created a new engine #",
            Log::ENGINE_COPIED => "Engine copied ",
            Log::ENGINE_DELETED => "Engine deleted #",
            Log::ENGINE_UPDATED => "Engine updated #",
            Log::DOMAIN_ADDED => "Created a new domain #",
            Log::DOMAIN_DELETED => "Domain deleted #",
            Log::DOMAIN_UPDATED => "Domain updated #",
            Log::DOMAINDATA_ADDED => "Added a new domain data #",
            Log::DOMAINDATA_DELETED => "Domain data deleted #",
            Log::DOMAINDATA_UPDATED => "Domain data updated #",
            Log::CONSUMER_ADDED => "Added a new relation #",
            Log::CONSUMER_REMOVED => "Relation removed #",
            Log::CONSUMER_UPDATED => "Relation updated #"
        ];

        return $actionList;
    }

    /**
     * @param $array
     * @return array
     */
    public static function removeObjectsFromArray($array)
    {
        // Remove from them database part
        $result = [];

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    continue;
                }

                if (strpos($key, 'Database') === false) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param $initialObject
     * @param $modifiedObject
     * @return array|null
     */
    public static function getObjectDifferences($initialObject, $modifiedObject)
    {
        // Cast the objects to array
        $initial = (array)$initialObject;
        $modified = (array)$modifiedObject;

        // Remove from them database part
        $initial = Log::removeObjectsFromArray($initial);
        $modified = Log::removeObjectsFromArray($modified);

        // Get differences
        $differences = [];
        try {
            $arrayDifferences = array_diff($modified, $initial);

            if (!empty($arrayDifferences) && is_array($arrayDifferences)) {
                foreach ($arrayDifferences as $key => $value) {
                    // The key contains the table name and column name. Lets remove the table name
                    $keyModified = explode("\0", trim($key));
                    $originalValue = !empty($initial[$key]) ? $initial[$key] : 0;
                    // Check if its not a password. We don't want to store in the log passwords
                    if (strpos($key, 'password') !== false) {
                        $value = 'new password';
                        $originalValue = 'password';
                    }

                    if (strpos($key, 'email') !== false) {
                        $value = 'new email';
                        $originalValue = 'email';
                    }

                    $differences[empty($keyModified[1]) ? $key : $keyModified[1]] = $originalValue . ' => ' . $value;
                }
            }

        } catch (Exception $e) {
            error_log($e);
        }

        return count($differences) > 0 ? $differences : null;
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param null $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param null $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param null $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return null
     */
    public function getDifferences()
    {
        return $this->differences;
    }

    /**
     * @param null $differences
     */
    public function setDifferences($differences)
    {
        $this->differences = $differences;
    }
}