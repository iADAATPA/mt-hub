<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'accounts';
$groups = new Groups();
$groupList = $groups->getAll();

$primaryKey = 'accounts.id';

$columns = [
    ['db' => 'accounts.id', 'dt' => 0],
    ['db' => 'accounts.id', 'dt' => 1],
    ['db' => 'accounts.active', 'dt' => 2],
    [
        'db' => 'accounts.name',
        'dt' => 3,
        'formatter' => function ($d, $row) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ],
    [
        'db' => 'accounts.groupid',
        'dt' => 4,
        'formatter' => function ($d, $row) {
            if (empty($GLOBALS['groupList'][$d]['name'])) {
                return $d;
            } else {
                return $GLOBALS['groupList'][$d]['name'];
            }
        }
    ],
    ['db' => 'accounts.adminid', 'dt' => 5],
    ['db' => 'accounts.created', 'dt' => 6],
    ['db' => 'accounts.id', 'dt' => 7],
    ['db' => 'accounts.id', 'dt' => 8],
    ['db' => 'accounts.deleted', 'dt' => 9],
    ['db' => 'accounts.groupid', 'dt' => 10],
];

$dataTables->getData($table, $primaryKey, $columns);
