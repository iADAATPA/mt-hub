<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$engineId = empty($_POST['id']) ? Session::getActiveEngineId() : $_POST['id'];
$engines = new Engines($engineId);
$name = $engines->getName();
$engines->setDeleted(Helper::getMySqlCurrentTime());
$response = $engines->update();

if (!$response || !$engineId) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    Log::save(Log::ENGINE_DELETED, $engineId);

    $returnCalls->setMessage(Session::t('Engine [' . $name . '] has been deleted.'));
}

$returnCalls->getResponse();
