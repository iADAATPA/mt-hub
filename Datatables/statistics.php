<?php

include_once '../functions.php';

Session::authenticateUser();

$type = empty($_GET['type']) ? StatisticsSummary::TYPE_SUPPLIER : $_GET['type'];
$type = in_array($type,
    [StatisticsSummary::TYPE_SUPPLIER, StatisticsSummary::TYPE_CONSUMER]) ? $type : StatisticsSummary::TYPE_SUPPLIER;

$columns = [
    ['label' => ''],
    ['label' => Session::t('Time')],
    ['label' => $type == StatisticsSummary::TYPE_SUPPLIER ? StatisticsSummary::TYPE_CONSUMER . ' Id' : StatisticsSummary::TYPE_SUPPLIER . ' Id'],
    ['label' => $type == StatisticsSummary::TYPE_SUPPLIER ? StatisticsSummary::TYPE_CONSUMER . ' Name' : StatisticsSummary::TYPE_SUPPLIER . ' Name'],
    ['label' => Session::t('Method')],
    ['label' => Session::t('Engine Id')],
    ['label' => Session::t('Engine Name')],
    ['label' => Session::t('Request Count')],
    ['label' => Session::t('Word Count')]
];

$formatters = [
    ['targets' => 0, 'visible' => 'false'],
    ['targets' => 1, 'width' => 15],
    ['targets' => 2, 'width' => 5, 'class' => 'dt-body-right'],
    ['targets' => 3, 'width' => 20, 'render' => 'formatLongString', 'class' => 'dt-body-left'],
    ['targets' => 5, 'width' => 5, 'class' => 'dt-body-right'],
    ['targets' => 6, 'render' => 'formatLongString', 'class' => 'dt-body-left'],
    ['targets' => 7, 'width' => 10, 'class' => 'dt-body-right'],
    ['targets' => 8, 'width' => 10, 'class' => 'dt-body-right'],
];

$table = new DataTables();
$table->setTableId('tableStatistics');
$table->setAjaxCallBack('Datatables/Ajax/statistics.php?type=' . $type);
$table->setPaging(false);
$table->setSortCol(1);
$table->setLengthChange(false);
$table->setEnableBtnDownloadExcel(true);
$table->setServerProcessing(false);
$table->setSortOrder('desc');
$table->setTableColumns($columns);
$table->setFormatters($formatters);
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>
