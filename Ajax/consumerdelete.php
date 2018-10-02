<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$id = empty($_POST['id']) ? null : $_POST['id'];
$relations = new Relations($id);
$response = $relations->delete();

if (!$response || !$id) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    Log::save(Log::URLCONFIG_DELETED, $id);

    $returnCalls->setMessage(Session::t('Consumer has been deleted.'));
}

$returnCalls->getResponse();
