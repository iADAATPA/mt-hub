<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$table = empty($_REQUEST['table']) ? null : $_REQUEST['table'];
$fileNames = empty($_REQUEST['filenames']) ? [] : $_REQUEST['filenames'];
$fileSizes = empty($_REQUEST['filesizes']) ? [] : $_REQUEST['filesizes'];
$fileList = "";
$fileNames = explode(";", $fileNames);
$fileSizes = explode(";", $fileSizes);

switch ($table) {
    case 'users':
        $users = new Users(Session::getUserId());
        $url = isset($fileNames[0]) ? 'Images/Users/' . $fileNames[0] : $users->getPhoto();
        $users->setPhoto($url);
        $users->update();
        Session::setUserPhoto($url);

        echo $url;
        return;

        break;
    case 'accounts':
        $accounts = new Accounts(Session::getAccountId());
        $url = isset($fileNames[0]) ? 'Images/Accounts/' . $fileNames[0] : $accounts->getLogo();
        $accounts->setLogo($url);
        $accounts->update();

        echo $url;
        return;

        break;
    default:
        return false;
}
