<?php

/**
 * Class Csrf
 * @author Marek Mazur
 */
class Csrf
{
    /**
     * Error
     */
    const UNAUTHORIZED_ACTION = "Unauthorized action.";

    /**
     * Generates csrf token
     *
     * @return string
     */
    private static function generateToken()
    {
        return base64_encode(openssl_random_pseudo_bytes(24));
    }

    /**
     * Sets csrfToken in a session
     */
    public static function setToken()
    {
        if (empty($_SESSION['csrfToken'])) {
            $_SESSION['csrfToken'] = self::generateToken();
        }
    }

    /**
     * Gets csrfToken from session
     *
     * @return null
     */
    public static function getToken()
    {
        return empty($_SESSION['csrfToken']) ? null : $_SESSION['csrfToken'];
    }

    /**
     * Validates hased csrf token. It checks for a $_REQUEST parameters: csrfToken and formName.
     * If validation failed throws exception.
     *
     * @return bool
     * @throws Exception
     */
    public static function validateToken()
    {
        $csrfToken = empty($_REQUEST['csrfToken']) ? null : $_REQUEST['csrfToken'];
        $formName = empty($_REQUEST['formName']) ? null : $_REQUEST['formName'];

        if ($csrfToken === self::hashToken($formName)) {
            return true;
        }

        throw new Exception(self::UNAUTHORIZED_ACTION);
    }

    /**
     * Hash csrf token using form name
     *
     * @param $string
     * @return string
     */
    private static function hashToken($string)
    {
        return hash("ripemd160", self::getToken() . $string);
    }

    /**
     * Prints csrf token and form name inside a hidden input fields
     *
     * @param $formName
     */
    public static function printFormInputs($formName)
    {
        echo '<input type="hidden" name="formName" id="formName" value="' . $formName . '" />';
        echo '<input type="hidden" name="csrfToken" id="csrfToken" value="' . self::hashToken($formName) . '" />';
    }

    /**
     * Prints csrf token and form name in the following format: formName: "NAME", csrfToken: "TOKEN"
     *
     * @param $formName
     */
    public static function printParameters($formName)
    {
        echo 'formName: "' . $formName . '", csrfToken: "' . self::hashToken($formName) . '"';
    }

    /**
     * Adds csrf token and form name to url in the following format: "&formName=NAME&csrfToken=TOKEN
     *
     * @param $formName
     * @return string
     */
    public static function printUrlParameters($formName)
    {
        return '&formName=' . $formName . '&csrfToken=' . self::hashToken($formName);
    }
}
