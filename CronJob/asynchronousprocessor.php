<?php

include_once '../functions.php';

// Make sure you have a cron job set on the server to run the script
// */5 * * * * curl https://iadaatpa.ie/CronJob/asynchronousprocessor.php &> /tmp/cron.out

// Get all the requests form the db
$asynchronousRequests = new AsynchronousRequests();
$records = $asynchronousRequests->getAll();

if ($records && is_array($records)) {
    // Process each request
    foreach ($records as $request) {
        if (!in_array($request['methodid'], [UrlConfig::METHOD_TRANSLATE_ID, UrlConfig::METHOD_ATRANSLATE_WITH_QES_ID])) {
            continue;
        }

        // check if the request is ready for deletion or translation
        if (!empty($request['requesttime']) && (strtotime($request['requesttime']) < strtotime('+2 hours'))) {
            $asynchronousRequest = new AsynchronousRequests($request['id']);
            $asynchronousRequest->delete();
        } elseif (empty($request['translationtime'])){
            // Get access details
            $relations = new Relations();
            $relations->setSupplierAccountId($request['supplieraccountid']);
            $relations->setConsumerAccountId($request['consumeraccountid']);
            $details = $relations->getSupplierConsumerRelation();
            $relations->set($details);

            $api = new ApiDev();
            $response = new \Slim\Http\Response();
            $api->apiResponses()->setResponse($response);
            $api->setDomainName($request['domain']);
            $api->setTarget($request['trg']);
            $api->setSource($request['src']);
            $api->setSegments(json_decode($request['text'], true));
            $api->setEngineCustomId($request['enginecustomid']);
            $api->setEngineName($request['enginename']);
            $api->setSupplierAccountId($request['supplieraccountid']);
            $api->setSupplierToken(Encryption::decrypt($relations->getApiToken(), $relations->getToken()));
            $api->setPassword(Encryption::decrypt($relations->getPassword(), $relations->getToken()));
            $api->setUserName($relations->getUserName());
            $api->setGuId($request['id']);

            $translatedSegments = $api->makeRequest(UrlConfig::METHOD_TRANSLATE_ID);

            $asynchronousRequest = new AsynchronousRequests($request['id']);
            $asynchronousRequest->setRetry(empty($asynchronousRequest->getRetry()) ? 1 : $asynchronousRequest->getRetry() + 1);

            if ((is_object($translatedSegments) || empty($translatedSegments) || $translatedSegments == "{}") && $asynchronousRequest->getRetry() > 10) {
                $asynchronousRequest->setError($api->apiResponses()->getError());
                $asynchronousRequest->setStatus($api->apiResponses()->getStatusCode());
                $asynchronousRequest->setTranslationTime(date('Y-m-d H:i:s'));
            } else {
                $asynchronousRequest->setTranslation(json_encode($translatedSegments));
                $asynchronousRequest->setTranslationTime(date('Y-m-d H:i:s'));
                $asynchronousRequest->setStatus(ApiResponses::HTTP_200_CODE);

            }

            $asynchronousRequest->update();
        }
    }
}
