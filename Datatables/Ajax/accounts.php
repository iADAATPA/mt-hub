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
    [
        'db' => 'accounts.id',
        'dt' => 7,
        'formatter' => function ($d) {
            $icon = '<span id="btnEdit_' . $d . '" title="' . Session::t('Edit properties') . '" onClick="editAccount(' . $d . ')"><i class="fa fa-lg fa-pencil-square-o fa-pointer" aria-hidden="true"></i></span>';

            return $icon;
        }
    ],
    [
        'db' => 'accounts.id',
        'dt' => 8,
        'formatter' => function ($d) {
            $icon = '<span title="' . Session::t('Login as a User') . '" onClick="loginToAccount(' . $d . ')"><i class="fa fa-lg fa-sign-in fa-green fa-pointer" aria-hidden="true"></i></a>';

            return $icon;
        }
    ],
    ['db' => 'accounts.deleted', 'dt' => 9],
    ['db' => 'accounts.groupid', 'dt' => 10],
];

$dataTables->getData($table, $primaryKey, $columns);
