<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$type = empty($_REQUEST['type']) ? StatisticsSummary::TYPE_SUPPLIER : $_REQUEST['type'];
$type = in_array($type,
    [StatisticsSummary::TYPE_SUPPLIER, StatisticsSummary::TYPE_CONSUMER]) ? $type : StatisticsSummary::TYPE_SUPPLIER;

$table = 'statisticssummary';

$primaryKey = 'statisticssummary.id';

$accounts = new Accounts();
$accountList = $accounts->getAll();

$methodList = UrlConfig::getApiMethods();

$engines = new Engines();
$engineList = $engines->getAll();

$columns = [
    ['db' => 'statisticssummary.id', 'dt' => 0],
    ['db' => 'statisticssummary.time', 'dt' => 1],
    [
        'db' => $type == StatisticsSummary::TYPE_SUPPLIER ? 'statisticssummary.consumeraccountid' : 'statisticssummary.supplieraccountid',
        'dt' => 2,
    ],
    [
        'db' => $type == StatisticsSummary::TYPE_SUPPLIER ? 'statisticssummary.consumeraccountid' : 'statisticssummary.supplieraccountid',
        'dt' => 3,
        'formatter' => function ($d) {
            return isset($GLOBALS['accountList'][$d]['name']) ? $GLOBALS['accountList'][$d]['name'] : $d;
        }
    ],
    [
        'db' => 'statisticssummary.methodid',
        'dt' => 4,
        'formatter' => function ($d) {
            return isset($GLOBALS['methodList'][$d]) ? $GLOBALS['methodList'][$d] : $d;
        }
    ],
    ['db' => 'statisticssummary.engineid', 'dt' => 5],
    [
        'db' => 'statisticssummary.engineid',
        'dt' => 6,
        'formatter' => function ($d) {
            return isset($GLOBALS['engineList'][$d]['name']) ? $GLOBALS['engineList'][$d]['name'] : $d;
        }
    ],
    [
        'db' => 'statisticssummary.requestcount',
        'dt' => 7,
        'formatter' => function ($d) {
            return number_format($d);
        }
    ],
    [
        'db' => 'statisticssummary.wordcount',
        'dt' => 8,
        'formatter' => function ($d) {
            return number_format($d);
        }
    ]
];

if ($type == StatisticsSummary::TYPE_SUPPLIER) {
    $userSearch = ' statisticssummary.supplieraccountid=' . Session::getAccountId();
} else {
    $userSearch = ' statisticssummary.consumeraccountid=' . Session::getAccountId();
}

$dataTables->getData($table, $primaryKey, $columns, $userSearch);
