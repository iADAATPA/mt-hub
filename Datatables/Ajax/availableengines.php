<?php

include_once "../../functions.php";

Session::authenticateUser();
Csrf::validateToken();

$response = [
    "aaData" => [],
    "iTotalDisplayRecords" => 0,
    "iTotalRecords" => 0,
    "sEcho" => 0
];

$engines = new Engines();
$allAccountEngines = $engines->getAllConsumerEngines(Session::getAccountId());

$domains = new Domains();
$allDomains = $domains->getAll();

$accounts = new Accounts();
$allAccounts = $accounts->getAll();

$id = 1;

if ($allAccountEngines && is_array($allAccountEngines)) {
    foreach ($allAccountEngines as $engine) {
        if ($engine['deleted']) {
            continue;
        }

        $metaData = new MetaData();
        $metaData->setEngineId($engine['id']);
        $metaDataAll = $metaData->getAll();
        $meta = null;

        if (is_array($metaDataAll)) {
            foreach ($metaDataAll as $data) {
                $variableId = $data['variable'];
                $value = $data['value'];
                $meta[$variableId] = $value;
            }
        }

        $reviewerData[$id] = [
            $engine['id'],
            $engine['id'],
            $engine['id'],
            $engine['name'],
            $engine['src'],
            $engine['trg'],
            $engine['type'] == Engines::ENGINE_TYPE_SMT ? 'SMT' : 'NMT',
            empty($allAccounts[$engine['accountid']]['name']) ? 'N/A' : $allAccounts[$engine['accountid']]['name'],
            empty($allDomains[$engine['domainid']]['name']) ? '' : $allDomains[$engine['domainid']]['name'],
            empty($engine['trainingwordcount']) ? '' : $engine['trainingwordcount'],
            empty($engine['fmeasure']) ? '' : $engine['fmeasure'] . '%',
            empty($engine['bleu']) ? '' : $engine['bleu'] . '%',
            empty($engine['ter']) ? '' : $engine['ter'] . '%',
            $meta,
            $engine['online']
        ];

        $response['aaData'][] = $reviewerData[$id];
        $id++;
    }
}

$response['iTotalRecords'] = count($response['aaData']);

echo json_encode($response);
