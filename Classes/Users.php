<?php

/**
 * Class Users
 * @package Classes
 * @author Marek Mazur
 */
class Users extends Database
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
     * @var null|string
     */
    private $name = null;

    /**
     * @var null|string
     */
    private $email = null;

    /**
     * @var null|string
     */
    private $password = null;

    /**
     * @var null|string
     */
    private $token = null;

    /**
     * @var null|int
     */
    private $loginAttempts = null;

    /**
     * @var null|string
     */
    private $lastlogin = null;

    /**
     * @var null|string
     */
    private $photo = null;

    /**
     * @var null|string
     */
    private $created = null;

    /**
     * Users constructor.
     * @param null $id
     */
    public function __construct($id = null, $name = null, $token = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setToken($token);
        $this->set($this->get());
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getEmail())) {
            $query = 'INSERT INTO
							users(
								accountid,
								name,
								email,
								password,
								token,
								loginattempts,
								lastlogin,
								photo,
								created
						)
						VALUES (
                            :accountid,
                            :name,
                            :email,
                            :password,
                            :token,
                            :loginattempts,
                            :lastlogin,
                            :photo,
                            NOW()
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':email', $this->getEmail(), PDO::PARAM_STR);
            $this->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);
            $this->bindValue(':token', $this->getToken(), PDO::PARAM_STR);
            $this->bindValue(':loginattempts', $this->getLoginAttempts(), PDO::PARAM_INT);
            $this->bindValue(':lastlogin', $this->getLastlogin(), PDO::PARAM_INT);
            $this->bindValue(':photo', $this->getPhoto(), PDO::PARAM_STR);
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
        if (!empty($this->getId()) || !empty($this->getName()) || !empty($this->getToken())) {
            $query = 'SELECT
                        id,
                        accountid,
                        name,
                        email,
                        password,
                        token,
                        loginattempts,
                        lastlogin,
                        photo,
                        created
					FROM
						users
					WHERE 
					    id    = :id
					OR  
					    name  = :name
                    OR
                        token = :token';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':token', $this->getToken(), PDO::PARAM_STR);
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
                    email,
                    password,
                    token,
                    loginattempts,
                    lastlogin,
                    photo,
                    created
                FROM
                    users';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $users = [];

            foreach ($result as $row) {
                $users[$row['id']] = $row;
            }

            return $users;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAccountUsers()
    {
        if (!empty($this->getAccountId())) {
            $query = 'SELECT
                    id,
                    accountid,
                    name,
                    email,
                    password,
                    token,
                    loginattempts,
                    lastlogin,
                    photo,
                    created
                FROM
                    users
                WHERE
                    accountid = :accountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            if ($result) {
                $users = [];

                foreach ($result as $row) {
                    $users[$row['id']] = $row;
                }

                return $users;
            }
        }

        return null;
    }

    public function delete()
    {
        if (!empty($this->getId())) {
            $query = 'DELETE FROM
						users
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

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId()) {
            $query = 'UPDATE
							users
						SET
							accountid     = :accountid,
                            name          = :name,
                            email         = :email,
                            password      = :password,
                            token         = :token,
                            loginattempts = :loginattempts,
                            lastlogin     = :lastlogin,
                            photo         = :photo
						WHERE
							id			  = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':email', $this->getEmail(), PDO::PARAM_STR);
            $this->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);
            $this->bindValue(':token', $this->getToken(), PDO::PARAM_STR);
            $this->bindValue(':loginattempts', $this->getLoginAttempts(), PDO::PARAM_INT);
            $this->bindValue(':lastlogin', $this->getLastlogin(), PDO::PARAM_STR);
            $this->bindValue(':photo', $this->getPhoto(), PDO::PARAM_STR);
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
            $this->setEmail($details['email'] ? $details['email'] : null);
            $this->setPassword($details['password'] ? $details['password'] : null);
            $this->setToken($details['token'] ? $details['token'] : null);
            $this->setLoginAttempts($details['loginattempts'] ? $details['loginattempts'] : null);
            $this->setLastlogin($details['lastlogin'] ? $details['lastlogin'] : null);
            $this->setPhoto($details['photo'] ? $details['photo'] : null);
            $this->setCreated($details['created'] ? $details['created'] : null);

            return true;
        }

        return false;
    }

    /**
     * Verification method using bcrypt.
     *
     * @param string $password
     */
    public function verifyPassword($password)
    {
        $dbpassword = $this->getPassword();
        // Verify stored password against plain-text password
        if (password_verify($password, $dbpassword)) {
            // Check if a newer hashing algorithm is available
            // or the cost option has changed
            if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
                // If so, create a new hash, and replace the old one
                $newHash = $this->hashPassword($password);
                $this->setPassword($newHash);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $length
     * @return string
     */
    static public function generateToken($length = 20)
    {
        $options = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $salt = '';

        for ($i = 0; $i <= $length; $i++) {
            $options = str_shuffle($options);
            $salt .= $options [rand(0, 61)];
        }
        return $salt;
    }

    /**
     * Hash password
     */
    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param null $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
        if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
            // If so, create a new hash, and replace the old one
            $password = $this->hashPassword($password);
        }

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

    /**
     * @return null
     */
    public function getLoginAttempts()
    {
        return $this->loginAttempts;
    }

    /**
     * @param null $loginAttempts
     */
    public function setLoginAttempts($loginAttempts)
    {
        $this->loginAttempts = $loginAttempts;
    }

    /**
     * @return null
     */
    public function getLastlogin()
    {
        return $this->lastlogin;
    }

    /**
     * @param null $lastlogin
     */
    public function setLastlogin($lastlogin)
    {
        $this->lastlogin = $lastlogin;
    }

    /**
     * @return null
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param null $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
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
}
