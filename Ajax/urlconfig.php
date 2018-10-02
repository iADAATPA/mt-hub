<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$methodId = empty($_POST['methodId']) || !is_numeric($_POST['methodId']) ? null : $_POST['methodId'];
$accountId = empty($_POST['accountId']) || !is_numeric($_POST['accountId']) ? Session::getAccountId() : $_POST['accountId'];

if (!$methodId) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
} else {
    $endPoint = !empty($_POST['endPoint']) ? trim($_POST['endPoint']) : null;
    $response = !empty($_POST['response']) ? trim($_POST['response']) : null;
    $request = !empty($_POST['request']) ? trim($_POST['request']) : null;
    $callback = !empty($_POST['callback']) ? trim($_POST['callback']) : null;
    $header = !empty($_POST['header']) ? trim($_POST['header']) : null;
    $basicAuth = empty($_POST['basicAuth']) ? null : 1;
    $digestAuth = empty($_POST['digestAuth']) ? null : 2;
    $authorization = empty($basicAuth) ? null : $basicAuth;
    $authorization = empty($authorization) && !empty($digestAuth) ? $digestAuth : $authorization;
    $id = !empty($_POST['id']) ? $_POST['id'] : null;
    $urlConfig = new UrlConfig($id);

    if (!empty($request)) {
        $request = $urlConfig->validateJson($request);

        if (empty($request)) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Invalid json format for Request'));
        }
    }

    if (!empty($header)) {
        $header = $urlConfig->validateJson($header);

        if (empty($header)) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Invalid json format for Header'));
        }
    }

    if (!empty($callback)) {
        $callback = $urlConfig->validateJson($callback);

        if (empty($callback)) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Invalid json format for Callback Parameters'));
        }
    }

    if ($returnCalls->getStatusId() != ReturnCalls::STATUSID_ERROR) {
        $urlConfigInitial = clone($urlConfig);
        $urlConfig->setMethodId($methodId);
        $urlConfig->setAccountId($accountId);
        $urlConfig->setType(UrlConfig::TYPE_POST);
        $urlConfig->setUrlEndPoint($endPoint);
        $urlConfig->setResponse($response);
        $urlConfig->setRequest($request);
        $urlConfig->setCallback($callback);
        $urlConfig->setAuthorization($authorization);
        $urlConfig->setHeader($header);
        $result = $id ? $urlConfig->update() : $urlConfig->insert();
        $message = Session::t('The URL configuration has been saved.');
        $returnCalls->setMessage($message);

        if (!$result) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('An unexpected error occurred'));
        } else {
            if ($id) {
                Log::save(Log::URLCONFIG_UPDATED, $id, Log::getObjectDifferences($urlConfigInitial, $urlConfig));
            } else {
                Log::save(Log::URLCONFIG_ADDED, $result);
            }
        }
    }
}

$returnCalls->getResponse();
