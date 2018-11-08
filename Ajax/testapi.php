<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();
$returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
$returnCalls->setMessage(Session::t('An unexpected error occurred'));

$segments = empty($_POST['segments']) ? null : $_POST['segments'];
$method = empty($_POST['method']) ? null : $_POST['method'];
$source = empty($_POST['source']) ? null : $_POST['source'];
$target = empty($_POST['target']) ? null : $_POST['target'];
$domain = empty($_POST['domain']) ? null : $_POST['domain'];
$customerId = empty($_POST['customerId']) ? null : $_POST['customerId'];

$accounts = new Accounts($customerId);
$apiToken = $accounts->getApiToken();

if ($method && $apiToken) {
    switch ($method) {
        case "detectLanguage":
            $url = "https://www.iadaatpa.eu/api/dev/detectlanguage";
            $data = [
                'segments[0]' => $segments,
                'token' => $apiToken
            ];
            break;
        case "detectDomain":
            $url = "https://www.iadaatpa.eu/api/dev/detectdomain";
            $data = [
                'segments[0]' => $segments,
                'token' => $apiToken,
                'source' => $source
            ];
            break;
        case "translate":
            $url = "https://www.iadaatpa.eu/api/dev/translate";
            $data = [
                'segments[0]' => $segments,
                'token' => $apiToken,
                'source' => $source,
                'target' => $target,
                'domain' => $domain
            ];
            break;
        case "aTranslate":
            $url = "https://www.iadaatpa.eu/api/dev/atranslate";
            $data = [
                'segments[0]' => $segments,
                'token' => $apiToken,
                'source' => $source,
                'target' => $target,
                'domain' => $domain
            ];
            break;
        case "aRetrieveTranslation":
            $url = "https://www.iadaatpa.eu/api/dev/aretrievetranslation/" . $apiToken . '/' . $segments;
            $data = null;
            break;
    }

    if (!empty($url)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
        }

        $response = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        $message = '';
        $returnCalls->setMessage($response);
        $returnCalls->setData(json_encode($curlInfo));
        $returnCalls->setStatusId(ReturnCalls::STATUSID_SUCCESS);
    }
}

$returnCalls->getResponse();
