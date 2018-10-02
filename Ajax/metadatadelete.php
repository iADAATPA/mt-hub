<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$engineId = empty($_POST['engineId']) || !is_numeric($_POST['engineId']) ? Session::getActiveEngineId() : $_POST['engineId'];
$id = empty($_POST['id']) || !is_numeric($_POST['id']) ? null : $_POST['id'];
$metaData = new MetaData($id);

if (!$id || $engineId != $metaData->getEngineId()) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    $result = $metaData->delete();

    if ($result) {
        $returnCalls->setMessage(Session::t('Metadata has been deleted.'));

        Log::save(Log::METADATA_DELETED, $id);
    } else {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    }
}

$returnCalls->getResponse();
