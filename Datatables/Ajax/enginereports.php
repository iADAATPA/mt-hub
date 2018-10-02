<?php

include_once "../../functions.php";

Session::authenticateUser();
Csrf::validateToken();

$response = array(
    "aaData" => array(),
    "iTotalDisplayRecords" => 0,
    "iTotalRecords" => 0,
    "sEcho" => 0
);

$engines = new Engines();
$engines->setAccountId(Session::getAccountId());
$allAccountEngines = $engines->getAllAccountEngines();

$domains = new Domains();
$allDomains = $domains->getAll();

$id = 1;

foreach ($allAccountEngines as $engine) {
    if ($engine['deleted']) {
        continue;
    }

    $reviewerData[$id] = [
        $engine['id'],
        $engine['id'],
        htmlentities($engine['name']),
        $engine['src'],
        $engine['trg'],
        empty($allDomains[$engine['domainid']]['name']) ? '' : $allDomains[htmlentities($engine['domainid'])]['name'],
        empty($engine['trainingwordcount']) ? '' : $engine['trainingwordcount'],
        empty($engine['fmeasure']) ? '' : $engine['fmeasure'] . '%',
        empty($engine['bleu']) ? '' : $engine['bleu'] . '%',
        empty($engine['ter']) ? '' : $engine['ter'] . '%',
    ];

    $response['aaData'][] = $reviewerData[$id];
    $id++;
}

$response['iTotalRecords'] = count($response['aaData']);

echo json_encode($response);
