<?php

/**
 * Class Session
 * @author Marek Mazur
 */
class Session
{
    /**
     * This is an important piece of security code. It prevents Session hijacking and
     * mirroring. It also presents session fixation (which in many cases leads to
     * denial of service events.
     */
    public static function secureSessionStart()
    {
        // Here is a fix for using the website on the localhost.
        $whitelist = [
            '127.0.0.1',
            '::1'
        ];

        // If the ip is not from a localhost set cookies params do Mt-HUB
        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            session_set_cookie_params(0, '/', Session::getSessionDomain(), true, true);
        } else {
            session_set_cookie_params(0, '/');
        }

        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id();
            $_SESSION['initiated'] = 1;
        }

        Csrf::setToken();
    }

    /**
     * @return string
     */
    public static function getSessionDomain()
    {
        $domainWhiteList = [
            'iadaatpa.com',
            'iadaatpa.org',
            'iadaatpa.eu',
            'app.iadaatpa.com',
            'app.iadaatpa.org',
            'app.iadaatpa.eu',
            'mt-hub.eu',
            'app.mt-hub.eu'
        ];

        $currentDomain = $_SERVER['SERVER_NAME'];

        if (in_array($currentDomain, $domainWhiteList)) {
            return $currentDomain;
        }

        return 'mt-hub.eu';
    }

    /**
     * Destroy session
     */
    public static function destroySession()
    {
        session_destroy();
        if (session_id() != "" || isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 2592000, '/', Session::getSessionDomain(), true, true);
        }
    }

    /**
     * Set website language
     */
    public static function setLang()
    {
        $supportedLangs = ['en-us', 'pl'];
        if (isset($_COOKIE['MT-HUBLang']) && in_array($_COOKIE['MT-HUBLang'], $supportedLangs)) {
            $_SESSION['lang'] = $_COOKIE['MT-HUBLang'];
        } else {
            if (empty($_SESSION['lang']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $browserLanguage = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

                foreach ($browserLanguage as $lang) {
                    $lang = strtolower($lang);
                    if (in_array($lang, $supportedLangs)) {
                        // Set the page locale to the first supported language found
                        $_SESSION['lang'] = $lang;
                        break;
                    }
                }
            }
        }

        // include Localfe file
        if (isset($_SESSION['lang'])) {
            $langFile = getDirectory() . '/Languages/' . $_SESSION['lang'] . '.php';

            if (file_exists($langFile)) {
                include_once $langFile;
            }
        }
    }

    /**
     * @return null
     */
    public static function getActiveSupplierName()
    {
        if (isset($_SESSION['activeSupplierName'])) {
            return $_SESSION['activeSupplierName'];
        }

        return null;
    }

    /**
     * @return null
     */
    public static function getActiveSupplierId()
    {
        if (isset($_SESSION['activeSupplierId'])) {
            return $_SESSION['activeSupplierId'];
        }

        return null;
    }

    /**
     * @param $email
     */
    public static function setUserEmail($email)
    {
        $_SESSION['email'] = $email;
    }

    /**
     * @return int|null
     */
    public static function getLogAccountId()
    {
        if (isset($_SESSION['logAccountId'])) {
            return (int)$_SESSION['logAccountId'];
        } elseif (isset($_SESSION['accountid'])) {
            return (int)$_SESSION['accountid'];
        }

        return null;
    }

    /**
     * @param $id
     */
    public static function setAccountId($id)
    {
        $_SESSION['accountid'] = $id;
        // Create another session for a user id that will be used only by the log class
        if (empty($_SESSION['logAccountId'])) {
            $_SESSION['logAccountId'] = $id;
        }
    }

    /**
     * @param $id
     */
    public static function setGroupId($id)
    {
        $_SESSION['groupid'] = $id;
    }

    /**
     * @return int|null
     */
    public static function getLogUserId()
    {
        if (isset($_SESSION['logUserId'])) {
            return ( int )$_SESSION['logUserId'];
        } elseif (isset($_SESSION['userid'])) {
            return ( int )$_SESSION['userid'];
        }

        return null;
    }

    /**
     * @param $id
     */
    public static function setUserId($id)
    {
        $_SESSION['userid'] = $id;
        // Create another session for a user id that will be used only by the log class
        if (empty($_SESSION['logUserId'])) {
            $_SESSION['logUserId'] = $id;
        }
    }

    /**
     * @return string
     */
    public static function getUserPhoto()
    {
        if (isset($_SESSION['photo'])) {
            return $_SESSION['photo'];
        }

        return 'Images/user.png';
    }

    /**
     * @param boolean $admin
     */
    public static function setAdmin($admin)
    {
        $_SESSION['admin'] = $admin;
    }

    /**
     * @param $name
     */
    public static function setUserName($name)
    {
        $_SESSION['user'] = $name;
    }

    /**
     * @param $id
     */
    public static function setActiveDomainId($id)
    {
        $_SESSION['domainId'] = $id;
    }

    /**
     * @return string
     */
    public static function getUserName()
    {
        if (Session::getLoginStatus()) {
            return $_SESSION['user'];
        }

        return '';

    }

    /**
     * @return bool
     */
    public static function getLoginStatus()
    {
        if (isset($_SESSION['user'])) {
            return true;
        }

        return false;
    }

    /**
     * @param $url
     */
    public static function setUserPhoto($url)
    {
        $_SESSION['photo'] = empty($url) ? 'Images/user.png' : $url;
    }

    /**
     * @return int|null
     */
    public static function getUserId()
    {
        if (isset($_SESSION['userid'])) {
            return ( int )$_SESSION['userid'];
        } elseif (isset($_SESSION['accountid'])) {
            return ( int )$_SESSION['accountid'];
        }

        return null;
    }

    /**
     * @return null
     */
    public static function getGroupId()
    {
        if (Session::getLoginStatus()) {
            return $_SESSION['groupid'];
        }

        return null;
    }

    /**
     * @return int|null
     */
    public static function getAccountId()
    {
        if (Session::getLoginStatus()) {
            return (int)$_SESSION['accountid'];
        }

        return null;
    }

    /**
     * @param $id
     */
    public static function setActiveEngineId($id)
    {
        $_SESSION['engineId'] = $id;
        // Set expiry date for cookie. Make it 01/01/2020 as when updating the cookie we will not change it
        $expDate = 1577836800;
        // Set cookies
        setcookie('engineId', $id, $expDate, '/', Session::getSessionDomain(), true, true);
    }

    /**
     * @return mixed
     */
    public static function getActiveDomainId()
    {
        if (!empty($_SESSION['domainId'])) {
            return $_SESSION['domainId'];
        }

        return null;
    }

    /**
     * @return int|null
     */
    public static function getActiveEngineId()
    {
        if (!empty($_SESSION['engineId'])) {
            return (int)$_SESSION['engineId'];
        }

        return null;
    }

    /**
     * @return bool
     */
    public static function isAdministrator()
    {
        if (Session::getGroupId() == Groups::GROUP_ADMINISTRATOR) {
            return true;
        }

        return false;
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function t($string)
    {
        if (isset($_SESSION['lang'])) {
            if (isset($_SESSION[$_SESSION['lang']][$string])) {
                return $_SESSION[$_SESSION['lang']][$string];
            }
        }

        return $string;
    }

    /**
     * @param null $userGroup
     */
    public static function authenticateUser($userGroup = null)
    {
        // Check if the file is accessed form index.php. We only allow access to the file if index.php
        $referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;

        if (!$referer) {
            session_destroy();
            http_response_code(404);
            header("Location: 404.php");
            exit();
        }

        if (!isset($_SESSION['user'])) {
            session_destroy();
            http_response_code(440);

            exit();
        }

        if (!Session::getLoginStatus()) {
            session_destroy();
            http_response_code(440);

            exit();
        }

        if ($userGroup && $_SESSION['groupid'] != $userGroup) {
            error_log("in");
            session_destroy();
            http_response_code(440);

            exit();
        }

        return;
    }
}
