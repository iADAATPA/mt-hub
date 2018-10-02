<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$domainId = empty($_POST['id']) ? null : $_POST['id'];
$segments = empty($_POST['segments']) ? null : Helper::sanitizeString($_POST['segments']);

if (!$domainId || !$segments || !Session::getAccountId()) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    $returnCalls->getResponse();
}

$domains = new Domains($domainId);

if ($domains->getName()) {
    $domainData = new DomainData();
    $domainData->setAccountId(Session::getAccountId());
    $domainData->setDomainId($domainId);
    $domainData->setSegments($segments);
    $response = $domainData->insert();

    if ($response) {
        Log::save(Log::DOMAINDATA_ADDED, $response);

        $domainData->deleteDomainDataExLastOne();

        $domainModels = new DomainModels();
        $response = $domainModels->compileDomainModels(Session::getAccountId());

        if ($response) {
            $message = Session::t('Segments have been added and all models were recompiled successfully.');
        } else {
            $message = Session::t('Segments have been added but some models were not recompiled successfully.');
        }

        $returnCalls->setMessage($message);
    }
}

if (empty($response)) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
}

$returnCalls->getResponse();
