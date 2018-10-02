<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'log';

$primaryKey = 'id';

$users = new Users();
$userList = $users->getAll();

$actionList = Log::getActionDescriptionList();

$columns = [
    ['db' => 'id', 'dt' => 0],
    ['db' => 'time', 'dt' => 1],
    [
        'db' => 'userid',
        'dt' => 2,
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
        'dt' => 3,
        'formatter' => function ($d, $row) {
            $action = isset($GLOBALS['actionList'][$d]) ? $GLOBALS['actionList'][$d] . $row['comment'] : $d;
            $differencesString = "";

            if (!empty($row['differences'])) {
                $differences = json_decode($row['differences'], true);

                if (is_array($differences)) {
                    $differencesString = '<br/>Differences:';
                    foreach ($differences as $key => $value) {
                        $differencesString .= '<br/>' . $key . ": " . $value;
                    }
                }
            }

            return htmlentities($action) . htmlentities($differencesString);
        }
    ],
    ['db' => 'comment', 'dt' => 4],
    ['db' => 'differences', 'dt' => 5],
];

$userSearch = ' accountid = ' . Session::getAccountId() . ' OR accountid LIKE "%=>' . Session::getAccountId() . '" ';

$dataTables->getData($table, $primaryKey, $columns, $userSearch);
