<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$engineId = empty($_POST['id']) || !is_numeric($_POST['id']) ? null : $_POST['id'];
$engines = new Engines($engineId);

if (!$engineId) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    $returnCalls->getResponse();
}

foreach ($_POST as $id => $metadata) {
    if (is_array($metadata)) {
        $value = empty($metadata['value']) ? null : $metadata['value'];
        $variable = empty($metadata['variable']) ? $id : $metadata['variable'];
        $recordId = empty($metadata['id']) ? null : $metadata['id'];

        if (empty($variable) || empty($value)) {
            continue;
        }
        $metaData = new MetaData($recordId);
        $metaData->setEngineId($engineId);
        $metaData->setValue($value);
        $metaData->setVariable($variable);
        $response = $recordId ? $metaData->update() : $metaData->insert();
    }
}

$returnCalls->setMessage(Session::t('Metadata has been added.'));

if (!$response) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    Log::save($recordId ? Log::METADATA_UPDATED : Log::METADATA_ADDED, $recordId ? $recordId : $response);
}

$returnCalls->getResponse();