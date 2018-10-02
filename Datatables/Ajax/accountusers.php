<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'users';

$primaryKey = 'users.userid';

$columns = [
    ['db' => 'users.userid', 'dt' => 0],
    ['db' => 'users.userid', 'dt' => 1],
    [
        'db' => 'users.name',
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
    ['db' => 'users.email', 'dt' => 3],
    ['db' => 'users.email', 'dt' => 4],
    ['db' => 'users.userid', 'dt' => 5]
];

$accountId = isset($_GET['accountid']) ? $_GET['accountid'] : Session::getAccountID();
$userSearch = "users.accountid = '$accountId' AND users.userid != ( SELECT adminid FROM accounts WHERE accountid = '$accountId' )";

$dataTables->getData($table, $primaryKey, $columns, $userSearch);
