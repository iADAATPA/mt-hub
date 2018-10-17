<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$id = empty($_POST['id']) || !is_numeric($_POST['id']) ? null : $_POST['id'];
$relations = new Relations($id);
$consumerAccountId = empty($_POST['consumerId']) ? $relations->getConsumerAccountId() : $_POST['consumerId'];
$consumerAccountId = empty($consumerAccountId) ? Session::getAccountId() : $consumerAccountId;
$supplierAccountId = empty($_POST['supplierId']) ? $relations->getSupplierAccountId() : $_POST['supplierId'];
$supplierAccountId = empty($supplierAccountId) ? Session::getAccountId() : $supplierAccountId;
$supplierApiToken = isset($_POST['supplierToken']) ? $_POST['supplierToken'] : $relations->getSupplierApiToken();
$token = empty($_POST['apiToken']) ? null : $_POST['apiToken'];
$userName = empty($_POST['userName']) ? null : $_POST['userName'];
$password = empty($_POST['password']) ? null : $_POST['password'];


$key = Encryption::generateKey();
$relations->setToken($key);

if (!empty($token)) {
    $relations->setApiToken(Encryption::encrypt($token, $key));
}

if (!empty($password)) {
    $relations->setPassword(Encryption::encrypt($password, $key));
}

$relations->setUserName($userName);
$relations->setSupplierAccountId($supplierAccountId);
$relations->setConsumerAccountId($consumerAccountId);
$response = $id ? $relations->update() : $relations->insert();

$message = $id ? Session::t('Relation has been updated') : Session::t('Reltion has been added.');
$returnCalls->setMessage($message);

if (!$response) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    Log::save($id ? Log::CONSUMER_UPDATED : Log::CONSUMER_ADDED, $id ? $id : $response);
}

$returnCalls->getResponse();
