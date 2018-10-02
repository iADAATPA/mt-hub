<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'log';

$primaryKey = 'id';

$accounts = new Accounts();
$accountList = $accounts->getAll();

$users = new Users();
$userList = $users->getAll();

$actionList = Log::getActionDescriptionList();

$columns = [
    ['db' => 'id', 'dt' => 0],
    ['db' => 'id', 'dt' => 1],
    ['db' => 'time', 'dt' => 2],
    ['db' => 'accountid', 'dt' => 3],
    [
        'db' => 'accountid',
        'dt' => 4,
        'formatter' => function ($d) {
            if (strpos($d, '=>') !== false) {
                $accountIds = explode('=>', $d);
                $adminAccountName = isset($GLOBALS['accountList'][$accountIds[0]]['name']) ? $GLOBALS['accountList'][$accountIds[0]]['name'] : $d;
                $accountName = isset($GLOBALS['accountList'][$accountIds[1]]['name']) ? $GLOBALS['accountList'][$accountIds[1]]['name'] : $d;
                return $adminAccountName . ' AS ' . $accountName;
            } else {
                return isset($GLOBALS['accountList'][$d]['name']) ? $GLOBALS['accountList'][$d]['name'] : $d;
            }
        }
    ],
    ['db' => 'userid', 'dt' => 5],
    [
        'db' => 'userid',
        'dt' => 6,
        'formatter' => function ($d) {
            if (strpos($d, '=>') !== false) {
                $userIds = explode('=>', $d);
                $adminUserName = isset($GLOBALS['userList'][$userIds[0]]['name']) ? $GLOBALS['userList'][$userIds[0]]['name'] : $d;
                $userName = isset($GLOBALS['userList'][$userIds[1]]['name']) ? $GLOBALS['userList'][$userIds[1]]['name'] : $d;
                return $adminUserName . ' AS ' . $userName;
            } else {
                return isset($GLOBALS['userList'][$d]['name']) ? $GLOBALS['userList'][$d]['name'] : $d;
            }
        }
    ],
    [
        'db' => 'action',
        'dt' => 7,
        'formatter' => function ($d, $row) {
            $action = isset($GLOBALS['actionList'][$d]) ? $GLOBALS['actionList'][$d] . $row['comment'] : $d;
            $differencesString = "";

            if (!empty($row['differences'])) {
                $differences = json_decode($row['differences'], true);

                if (is_array($differences)) {
                    $differencesString = '<br/>Differences:';
                    foreach ($differences as $key => $value) {
                        $differencesString .= '<br/>' . $key . ": " . htmlentities($value);
                    }
                }
            }

            return htmlentities($action) . htmlentities($differencesString);
        }
    ],
    [
        'db' => 'comment',
        'dt' => 8,
        'formatter' => function ($d) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ],
    ['db' => 'differences', 'dt' => 9],
];

$dataTables->getData($table, $primaryKey, $columns);
