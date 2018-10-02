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
        'db' => 'engines.name',
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
        'db' => 'domains.name AS domain',
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
    [
        'db' => 'engines.customid',
        'dt' => 4,
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
        'db' => 'engines.type',
        'dt' => 5,
        'formatter' => function ($d) {
            switch ($d) {
                case Engines::ENGINE_TYPE_SMT:
                    return 'SMT';
                    break;
                case Engines::ENGINE_TYPE_NMT:
                    return 'NMT';
                    break;
                case Engines::ENGINE_TYPE_RBMT:
                    return 'RBMT';
                    break;
                default:
                    return 'SMT';
                    break;
            }
        }
    ],
    ['db' => 'engines.src', 'dt' => 6],
    ['db' => 'engines.trg', 'dt' => 7],
    ['db' => 'engines.online', 'dt' => 8],
    ['db' => 'engines.id', 'dt' => 9],
    [
        'db' => 'engines.id',
        'dt' => 10,
        'formatter' => function ($d) {
            if ($d == Session::getActiveEngineId()) {
                return 1;
            } else {
                return 0;
            }
        }
    ]
];

$userSearch = ' engines.accountid=' . Session::getAccountId() . ' AND engines.deleted IS NULL';

$dataTables->getData($table, $primaryKey, $columns, $userSearch, $join);
