<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$accountId = empty($_POST['accountId']) || !is_numeric($_POST['accountId']) ? null : $_POST['accountId'];
$engineId = empty($_POST['id']) || !is_numeric($_POST['id']) ? Session::getActiveEngineId() : $_POST['id'];
$engines = new Engines($engineId);
$name = $engines->getName();
$newName = $engines->generateEngineCopyName($name);
$engines->setName($newName);
$engines->setAccountId($accountId ? $accountId : $engines->getAccountId());
$id = $engines->insert();

if (!$id || !$engineId) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    Log::save(Log::ENGINE_COPIED, '#' . $engineId . ' to #' . $id);

    $metaData = new MetaData();
    $metaData->setEngineId($engineId);
    $engineMetaData = $metaData->getAll();

    if ($engineMetaData && is_array($engineMetaData)) {
        $metaData->setEngineId($id);

        foreach ($engineMetaData as $data) {
            $metaData->setVariable($data['variable']);
            $metaData->setValue($data['value']);
            $response = $metaData->insert();

            if ($response) {
                Log::save(Log::METADATA_ADDED, $metaData->getVariable() . '=>' . $metaData->getValue());
            }
        }
    }

    $returnCalls->setMessage(Session::t('Engine') . ' [' . $name . '] ' . Session::t('has been copied as') . ' <b>' . $newName . '</b>.');
}

$returnCalls->getResponse();
