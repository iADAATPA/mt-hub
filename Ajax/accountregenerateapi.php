<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$accountId = Session::getAccountId();
$token = Users::generateToken(16);
$accounts = new Accounts($accountId);
$accounts->setApiToken($token);
$response = $accounts->update();

if ($response) {
    Log::save(Log::ACCOUNT_API_UPDATED);

    $returnCalls->setMessage(Session::t('API token has been regenerated.'));
    $returnCalls->setData($token);
} else {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
}

$returnCalls->getResponse();
