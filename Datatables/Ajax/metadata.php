<?php

include_once '../../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$dataTables = new DataTables();

$table = 'metadata';

$primaryKey = 'id';

$columns = [
    ['db' => 'id', 'dt' => 0],
    ['db' => 'id', 'dt' => 1],
    [
        'db' => 'variable',
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
        'db' => 'value',
        'dt' => 3,
        'formatter' => function ($d) {
            if ($d) {
                $d = !empty($d) ? htmlentities($d) : '';
                return $d;
            } else {
                return '';
            }
        }
    ]
];

$engineId = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : Session::getActiveEngineId();
$userSearch = ' engineid=' . $engineId;

$dataTables->getData($table, $primaryKey, $columns, $userSearch);
