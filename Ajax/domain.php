<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$domainId = empty($_POST['id']) ? null : $_POST['id'];
$domains = new Domains($domainId);
$name = empty($_POST['name']) ? $engines->getName() : $_POST['name'];
$source = empty($_POST['source']) ? $engines->getSource() : $_POST['source'];
$accountId = empty($_POST['owner']) ? Session::getAccountId() : $_POST['owner'];

$response = $domains->validateDomain($name, $source, $accountId);

if (!$response) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Not a unique domain name for a selected source language.'));
} else {
    if ($domainId) {
        $domains->setName($name);
        $domains->setSrc($source);
        $domains->setAccountId($accountId);
        $response = $domains->update();
    } else {
        $domains->setName($name);
        $domains->setSrc($source);
        $domains->setAccountId($accountId);
        $response = $domains->insert();
        Session::setActiveDomainId($response);

        Log::save(Log::DOMAIN_ADDED, $response);
    }

    $message = $domainId ? Session::t('Domain has been updated.') : Session::t('Domain has been added.');

    Log::save($domainId ? Log::DOMAIN_UPDATED : Log::DOMAIN_ADDED, $domainId ? $domainId : $response);

    $returnCalls->setMessage($message);

    if (!$response) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    }
}

$returnCalls->getResponse();
