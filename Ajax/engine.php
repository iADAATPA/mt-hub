<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$engineId = empty($_POST['id']) || !is_numeric($_POST['id']) ? null : $_POST['id'];
$engines = new Engines($engineId);
$enginesInitial = clone($engines);
$name = empty($_POST['name']) ? $engines->getName() : trim($_POST['name']);
$customId = empty($_POST['customId']) ? null : trim($_POST['customId']);
$type = empty($_POST['type']) ? $engines->getType() : $_POST['type'];
$source = empty($_POST['source']) ? $engines->getSource() : $_POST['source'];
$target = empty($_POST['target']) ? $engines->getTarget() : $_POST['target'];
$online = empty($_POST['online']) ? 0 : 1;
$domainId = empty($_POST['domainId']) || !is_numeric($_POST['domainId']) ? null : $_POST['domainId'];
$accountId = empty($_POST['accountId']) ? Session::getAccountId() : $_POST['accountId'];
$description = empty($_POST['description']) ? null : trim($_POST['description']);
$ter = empty($_POST['ter']) || !is_numeric($_POST['ter']) ? null : $_POST['ter'];
$bleu = empty($_POST['bleu']) || !is_numeric($_POST['bleu']) ? null : $_POST['bleu'];
$fmeasure = empty($_POST['fmeasure']) || !is_numeric($_POST['fmeasure']) ? null : $_POST['fmeasure'];
$trainingWordCount = empty($_POST['trainingwordcount']) || !is_numeric($_POST['trainingwordcount']) ? null : $_POST['trainingwordcount'];
$costPerWord = empty($_POST['costperword']) || !is_numeric($_POST['costperword']) ? null : $_POST['costperword'];

$engines->setAccountId($accountId);
$engines->setName($name);
$engines->setSource($source);
$engines->setTarget($target);
$engines->setType($type);
$engines->setOnline($online);
$engines->setCustomId($customId);
$engines->setDomainId($domainId);
$engines->setDescription($description);
$engines->setTer($ter);
$engines->setBleu($bleu);
$engines->setFmeasure($fmeasure);
$engines->setCostPerWord($costPerWord);
$engines->setTrainingWordCount($trainingWordCount);
$response = $engineId ? $engines->update() : $engines->insert();

$message = $engineId ? Session::t('Engine has been updated') : Session::t('Engine has been added.');
$returnCalls->setMessage($message);

if (!$response) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    if ($engineId) {
        Log::save(Log::ENGINE_UPDATED, $engineId, Log::getObjectDifferences($enginesInitial, $engines));
    } else {
        Log::save(Log::ENGINE_ADDED, $response);
    }
}

$returnCalls->getResponse();
