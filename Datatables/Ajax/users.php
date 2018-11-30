<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'users';

$primaryKey = 'id';

// Get Accounts
$accounts = new Accounts();
$accountList = $accounts->getAll();

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
    [
        'db' => 'email',
        'dt' => 3,
        'formatter' => function ($d) {
            $email = strlen($d) > 20 ? substr($d,  0, 17) . "..." : $d;
            $emailImg = '<i class="fa fa-lg fa-envelope-o fa-fw fa-deeporange fa-pointer" aria-hidden="true"></i>';

            $emailDisplay = '<span title="' . $d . '"><a href="mailto:\'' . $d . '\'">' .  $emailImg . '</a>&nbsp;&nbsp;' . $email . '</span>';

            return $emailDisplay;
        }
    ],
    ['db' => 'created', 'dt' => 4],
    ['db' => 'lastlogin', 'dt' => 5],
    [
        'db' => 'accountid',
        'dt' => 6,
        'formatter' => function ($d) {
            $name = empty($GLOBALS['accountList'][$d]['name']) ? "" : $GLOBALS['accountList'][$d]['name'];
            $accountlDisplay = '<span title="' . $name . '">' . $d . '</span>';

            return $accountlDisplay;
        }
    ],
    [
        'db' => 'accountid',
        'dt' => 7,
        'formatter' => function ($d) {
            $groupId = empty($GLOBALS['accountList'][$d]['groupid']) ? "" : $GLOBALS['accountList'][$d]['groupid'];

            return Groups::getGroupName($groupId);
        }
    ],
    [
        'db' => 'id',
        'dt' => 8,
        'formatter' => function ($d) {
            $icon = '<span id="btnEdit_' . $d . '" title="' . Session::t('Edit properties') . '" onClick="editUser(' . $d . ')"><i class="fa fa-lg fa-pencil-square-o fa-pointer" aria-hidden="true"></i></span>';

	   	    return $icon;
        }
    ],
    [
        'db' => 'id',
        'dt' => 9,
        'formatter' => function ($d, $row) {
            $icon = '<span title="' . Session::t('Login as a User') . '" onClick="loginToAccount(' . $row['accountid'] .  ',' . $d . ')"><i class="fa fa-lg fa-sign-in fa-green fa-pointer" aria-hidden="true"></i></a>';

            return $icon;
        }
    ],
    [
        'db' => 'name',
        'dt' => 10,
        'formatter' => function ($d, $row) {
            $icon = '<i onclick="resendPass(\'' . $d . '\')" title="' . Session::t('Resend set password email') . '" class="fa fa-lg fa-ticket fa-red fa-pointer" aria-hidden="true"></i>';

            return $icon;
        }
    ]
];

$userSearch = ' accountid IN (SELECT accountid FROM accounts WHERE deleted IS NULL)';

$dataTables->getData($table, $primaryKey, $columns, $userSearch);
