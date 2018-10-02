<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'requestlog';

$primaryKey = 'id';

$accounts = new Accounts();
$accountList = $accounts->getAll();

$methodList = UrlConfig::getApiMethods();

$columns = [
    ['db' => 'id', 'dt' => 0],
    ['db' => 'id', 'dt' => 1],
    ['db' => 'timein', 'dt' => 2],
    ['db' => 'timeout', 'dt' => 3],
    ['db' => 'timems', 'dt' => 4],
    ['db' => 'consumeraccountid', 'dt' => 5],
    [
        'db' => 'consumeraccountid',
        'dt' => 6,
        'formatter' => function ($d) {
            return isset($GLOBALS['accountList'][$d]['name']) ? $GLOBALS['accountList'][$d]['name'] : $d;
        }
    ],
    ['db' => 'supplieraccountid', 'dt' => 7],
    [
        'db' => 'supplieraccountid',
        'dt' => 8,
        'formatter' => function ($d) {
            return isset($GLOBALS['accountList'][$d]['name']) ? $GLOBALS['accountList'][$d]['name'] : $d;
        }
    ],
    ['db' => 'engineid', 'dt' => 9],
    ['db' => 'httpcode', 'dt' => 10],
    [
        'db' => 'methodid',
        'dt' => 11,
        'formatter' => function ($d) {
            $method = isset($GLOBALS['methodList'][$d]) ? $GLOBALS['methodList'][$d] : $d;

            return $method;
        }
    ],
    ['db' => 'id', 'dt' => 12],
    ['db' => 'src', 'dt' => 13],
    ['db' => 'trg', 'dt' => 14],
    ['db' => 'request', 'dt' => 15],
    ['db' => 'response', 'dt' => 16],
];

$dataTables->getData($table, $primaryKey, $columns);
