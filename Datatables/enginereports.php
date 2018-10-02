<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => '#', 'title' => Session::t('Engine Id'), 'class' => 'all'],
    ['label' => Session::t('Engine'), 'title' => Session::t('Engine Name'), 'class' => 'all'],
    ['label' => Session::t('Source'), 'title' => Session::t('Source Language')],
    ['label' => Session::t('Target'), 'title' => Session::t('Target Language')],
    ['label' => Session::t('Domain')],
    ['label' => Session::t('TWC'), 'title' => Session::t('Training Word Count')],
    ['label' => Session::t('F-Measure')],
    ['label' => Session::t('BLEU')],
    ['label' => Session::t('TER')],
];

$formatters = [
    ['targets' => 1, 'width' => 1],
    ['targets' => 2, 'width' => 20, 'render' => 'formatLongString'],
    ['targets' => 3, 'width' => 10, 'render' => 'formatFlag'],
    ['targets' => 4, 'width' => 10, 'render' => 'formatFlag'],
    ['targets' => 6, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 7, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 8, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 9, 'width' => 7, 'class' => 'dt-body-right'],
];

$table = new DataTables();
$table->setTableId('tableEngineReports');
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/enginereports.php');
$table->setPaging(false);
$table->setSortCol(2);
$table->setServerProcessing(false);
$table->setSortOrder('asc');
$table->setTableColumns($columns);
$table->setEnableBtnDownloadExcel(true);
$table->setFormatters($formatters);
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>
