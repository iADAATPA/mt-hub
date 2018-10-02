<?php

/**
 * Autoload classes
 */
spl_autoload_register(function ($className) {
    $classUrl = getDirectory() . 'Classes/' . $className . '.php';

    if ($className && file_exists($classUrl)) {
        include_once $classUrl;
    }
});

Session::secureSessionStart();

include_once getDirectory() . 'Vendor/autoload.php';

date_default_timezone_set('UTC');

// Set main directory path
define("FULL_PATH", getDirectory());
define('SITE_ROOT', realpath(dirname(__FILE__)));

// quickcheck to see if we're on a live server or localhost
$_SERVER['SERVER_NAME'] == "localhost" ? $ldir = '/iadaatpa' : $ldir = null;

$newPath = $_SERVER['DOCUMENT_ROOT'] . $ldir . '/\.';
set_include_path($newPath . PATH_SEPARATOR . get_include_path());


/**
 * Get main folder directory
 */
function getDirectory()
{
    $dir = null;

    if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') {
        $dir = $_SERVER["DOCUMENT_ROOT"] . '/iadaatpa';
    } elseif (isset($_SERVER["DOCUMENT_ROOT"]) && $_SERVER["DOCUMENT_ROOT"] != '') {
        $dir = $_SERVER["DOCUMENT_ROOT"];
    } else {
        $dir = dirname(__DIR__);
    }

    return $dir . '/';
}
