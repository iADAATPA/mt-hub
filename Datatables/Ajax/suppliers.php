<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'relations';
$join = 'INNER JOIN accounts ON (accounts.id = relations.supplieraccountid) LEFT JOIN users ON (accounts.adminid = users.id)';

$primaryKey = 'relations.id';

$columns = [
    ['db' => 'relations.id', 'dt' => 0],
    ['db' => 'relations.id', 'dt' => 1],
    ['db' => 'accounts.active', 'dt' => 2],
    [
        'db' => 'accounts.name',
        'dt' => 3,
        'formatter' => function ($d) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ],
    ['db' => 'users.email', 'dt' => 4],
    [
        'db' => 'users.name AS username',
        'dt' => 5,
        'formatter' => function ($d) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ],
    ['db' => 'relations.added', 'dt' => 6],
    ['db' => 'relations.id', 'dt' => 7]
];

$userSearch = ' relations.consumeraccountid=' . Session::getAccountId();

$dataTables->getData($table, $primaryKey, $columns, $userSearch, $join);
