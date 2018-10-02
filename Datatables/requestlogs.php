<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => '#'],
    ['label' => Session::t('Time In')],
    ['label' => Session::t('Time Out')],
    ['label' => Session::t('Time (ms)')],
    ['label' => Session::t('Consumer Id')],
    ['label' => Session::t('Consumer Name')],
    ['label' => Session::t('Supplier Id')],
    ['label' => Session::t('Supplier Name')],
    ['label' => Session::t('Engine Id')],
    ['label' => Session::t('HTTP')],
    ['label' => Session::t('Method')],
    ['label' => Session::t(''), 'title' => Session::t('Details')],
    ['label' => Session::t('Source')],
    ['label' => Session::t('Target')],
    ['label' => Session::t('Request')],
    ['label' => Session::t('Response')]
];

$foramtters = [
    ['targets' => 3, 'visible'=> 'false'],
    ['targets' => 12, 'width' => 1, 'render'=> 'formatRequestLog', 'orderable'=> 'false', 'class' => 'dt-body-center'],
    ['targets' => 13, 'visible'=> 'false'],
    ['targets' => 14, 'visible'=> 'false'],
    ['targets' => 15, 'visible'=> 'false'],
    ['targets' => 16, 'visible'=> 'false']
];

$tableId = 'tableRequestLogs';
$table = new DataTables();
$table->setTableId($tableId);
$table->setEnableBtnDownloadExcel(true);
$table->setFormatters($foramtters);
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/requestlogs.php');
$table->setSortCol(1);
$table->setSortOrder('desc');
$table->setTableColumns($columns);
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>
