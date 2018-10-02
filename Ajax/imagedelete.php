<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();
$returnCalls->setMessage(Session::t('The image has been deleted.'));

$table = empty($_POST['table']) ? null : $_POST['table'];

switch ($table) {
    case 'users':
        $users = new Users(Session::getUserId());
        $url = $users->getPhoto();
        if ($url && file_exists(getDirectory() . $url)) {
            unlink(getDirectory() . $url);
        }

        $users->setPhoto(null);
        $users->update();
        $url = 'Images/user.png';
        Session::setUserPhoto($url);

        break;
    case 'accounts':
        $accounts = new Accounts(Session::getAccountId());
        $url = $accounts->getLogo();
        if ($url && file_exists(getDirectory() . $url)) {
            unlink(getDirectory() . $url);
        }
        $accounts->setLogo(null);
        $accounts->update();

        break;
    default:
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('An unexpected error occurred'));
}

$returnCalls->getResponse();
