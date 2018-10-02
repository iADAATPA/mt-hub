<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'domains';
$join = ' LEFT JOIN domaindata ON (domains.id = domaindata.domainid) ';

$primaryKey = 'domains.id';

$columns = [
    ['db' => 'domains.id', 'dt' => 0],
    ['db' => 'domains.id', 'dt' => 1],
    [
        'db' => 'domains.name',
        'dt' => 2,
        'formatter' => function ($d, $row) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ],
    ['db' => 'domains.src', 'dt' => 3],
    [
        'db' => 'domaindata.added',
        'dt' => 4,
        'formatter' => function ($d, $row) {
            if (!empty($d)) {
                return 1;
            } else {
                return 0;
            }
        }
    ],
    [
        'db' => 'domaindata.id AS domainid',
        'dt' => 5,
        'formatter' => function ($d, $row) {
            if (!empty($d)) {
                return $d;
            } else {
                return 0;
            }
        }
    ],
    ['db' => 'domains.id', 'dt' => 6]
];

$userSearch = ' domains.accountid = ' . Session::getAccountId();

$dataTables->getData($table, $primaryKey, $columns, $userSearch, $join);
