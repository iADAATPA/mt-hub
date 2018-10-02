<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'relations';

$primaryKey = 'id';

$accounts = new Accounts();
$accountList = $accounts->getAll();

$columns = [
    ['db' => 'id', 'dt' => 0],
    ['db' => 'id', 'dt' => 1],
    [
        'db' => 'supplieraccountid',
        'dt' => 2,
        'formatter' => function ($d) {
            return isset($GLOBALS['accountList'][$d]['name']) ? $GLOBALS['accountList'][$d]['name'] : $d;
        }
    ],
    ['db' => 'supplieraccountid', 'dt' => 3],
    [
        'db' => 'consumeraccountid',
        'dt' => 4,
        'formatter' => function ($d) {
            return isset($GLOBALS['accountList'][$d]['name']) ? $GLOBALS['accountList'][$d]['name'] : $d;
        }
    ],
    ['db' => 'consumeraccountid', 'dt' => 5],
    [
        'db' => 'added',
        'dt' => 6,
        'formatter' => function ($d) {
            if ($d) {
                return $d;
            } else {
                return 'N/A';
            }
        }
    ]
];

$dataTables->getData($table, $primaryKey, $columns);
