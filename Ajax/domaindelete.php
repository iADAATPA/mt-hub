<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$domainId = empty($_POST['id']) ? null : $_POST['id'];
$domains = new Domains($domainId);

if ($domains->getAccountId() == Session::getAccountId()) {
    $response = $domains->delete();

    if (!$response) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    } else {
        Log::save(Log::DOMAIN_DELETED, $domainId);

        $domainData = new DomainData();
        $domainData->setAccountId(Session::getAccountId());
        $domainData->setDomainId($domainId);
        $response = $domainData->deleteDomainData();
        $domainModels = new DomainModels();
        $response = $domainModels->compileDomainModels(Session::getAccountId());

        if ($response) {
            $message = Session::t('Domain has been deleted and all models were recompiled successfully.');
        } else {
            $message = Session::t('Domain has been deleted but some models were not recompiled successfully.');
        }

        $returnCalls->setMessage($message);
    }
} else {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
}

$returnCalls->getResponse();
