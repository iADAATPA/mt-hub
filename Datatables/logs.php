<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => '#'],
    ['label' => Session::t('Time')],
    ['label' => Session::t('Account Id')],
    ['label' => Session::t('Account Name')],
    ['label' => Session::t('User Id')],
    ['label' => Session::t('User Name')],
    ['label' => Session::t('Action')],
];

$tableId = 'tableLogs';
$table = new DataTables();
$table->setTableId($tableId);
$table->setEnableBtnDownloadExcel(true);
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/logs.php');
$table->setSortCol(1);
$table->setSortOrder('desc');
$table->setPaging(false);
$table->setServerProcessing(false);
$table->setTableColumns($columns);
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>
