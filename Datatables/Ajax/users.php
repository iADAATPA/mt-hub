<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'users';

$primaryKey = 'id';

$columns = [
    ['db' => 'id', 'dt' => 0],
    ['db' => 'id', 'dt' => 1],
    [
        'db' => 'name',
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
    ['db' => 'email', 'dt' => 3],
    ['db' => 'created', 'dt' => 4],
    ['db' => 'lastlogin', 'dt' => 5],
    ['db' => 'accountid', 'dt' => 6],
    ['db' => 'email', 'dt' => 7],
    ['db' => 'email', 'dt' => 8],
    ['db' => 'id', 'dt' => 9]
];

$userSearch = ' accountid IN (SELECT accountid FROM accounts WHERE deleted IS NULL)';

$dataTables->getData($table, $primaryKey, $columns, $userSearch);
