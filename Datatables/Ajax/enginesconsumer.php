<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'engines';
$join = 'LEFT JOIN domains ON (domains.id = engines.domainid)';

$primaryKey = 'engines.id';

$columns = [
    ['db' => 'engines.id', 'dt' => 0],
    ['db' => 'engines.id', 'dt' => 1],
    [
        'db' => 'engines.name AS eng',
        'dt' => 2,
        'formatter' => function ($d) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ],
    ['db' => 'engines.src', 'dt' => 3],
    ['db' => 'engines.trg', 'dt' => 4],
    ['db' => 'engines.online', 'dt' => 5],
    ['db' => 'engines.id', 'dt' => 6],
    ['db' => 'domains.name', 'dt' => 7]
];

$userSearch = ' engines.accountid=' . Session::getActiveSupplierId() . ' AND engines.deleted IS NULL';

$dataTables->getData($table, $primaryKey, $columns, $userSearch, $join);
