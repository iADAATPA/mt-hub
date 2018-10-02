<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$id = empty($_POST['id']) ? null : $_POST['id'];
$userId = empty($_POST['userId']) ? null : $_POST['userId'];

if ($id) {
    Log::save(Log::ACCOUNT_SWITCHED, $id);

    $accounts = new Accounts($id);
    $userId = $userId ? $userId : $accounts->getAdminId();
    $users = new Users($userId);
    Session::setGroupID($accounts->getGroupId());
    Session::setUserName($users->getName());
    Session::setUserId($users->getId());
    Session::setUserPhoto($users->getPhoto());
    Session::setUserEmail($users->getEmail());
    Session::setAdmin(true);
    Session::setAccountID($id);

    $engines = new Engines();
    $engines->setAccountId($id);
    $details = $engines->getAllAccountEngines();
    if (is_array($details)) {
        reset($details);
        $key = key($details);
        $details = empty($details[$key]) ? null : $details[$key];
    }

    $engines->set($details);

    Session::setActiveEngineId($engines->getId());
} else {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Missing account Id'));
}

$returnCalls->getResponse();
