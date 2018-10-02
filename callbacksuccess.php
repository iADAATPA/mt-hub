<?php

include_once 'functions.php';

$request = $_REQUEST;

error_log("");
error_log( empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "REQUEST");

error_log("REQUEST: " . json_encode($_REQUEST));
error_log("BODY: " . file_get_contents('php://input'));

if (!empty($request)) {
    $id = empty($request["id"]) ? null : $request["id"];
    $supplierGuid = empty($request["request-id"]) ? null : $request["request-id"];

    $asynchronousRequest = new AsynchronousRequests($id);

    $translation = empty($request["translated-text"]) ? null : $request["translated-text"];
    $translation = empty($translation) && $asynchronousRequest->getMethodId() == UrlConfig::METHOD_ATRANSLATE_FILE_ID ? file_get_contents('php://input') : $translation;
    $translation = is_array($translation) ? $translation : [$translation];

    $requesLog = new RequestLog();
    $requesLog->setSupplierAccountId($asynchronousRequest->getSupplierAccountId());
    $requesLog->setEngineId($asynchronousRequest->getEngineName());
    $requesLog->setTrg($asynchronousRequest->getTarget());
    $requesLog->setSrc($asynchronousRequest->getSource());
    $requesLog->setMethodId($asynchronousRequest->getMethodId());
    $requesLog->setHttpCode(ApiResponses::HTTP_200_CODE);
    $requesLog->setTimeMs(1);
    $requesLog->setConsumerAccountId($asynchronousRequest->getConsumerAccountid());
    $requesLog->setResponse(json_encode($request));
    $requesLog->insert();

    $asynchronousRequest->setTranslationTime($asynchronousRequest->getCurrentMySqlTime());
    $asynchronousRequest->setTranslation(json_encode($translation));
    $asynchronousRequest->setStatus(ApiResponses::HTTP_200_CODE);
    $asynchronousRequest->update();
}
