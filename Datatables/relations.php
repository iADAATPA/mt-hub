<?php

include_once '../functions.php';

Session::authenticateUser();

$table = new DataTables();
$table->setTableId('tableRealtions');
$table->setEnableBtnDownloadExcel(true);
$table->setAjaxCallBack('Datatables/Ajax/relations.php');
$table->setSortCol(1);
$table->setSortOrder('desc');
$table->setServerProcessing(false);
$table->setLengthChange(false);
$table->setPaging(false);

$columns = [
    ['label' => ''],
    ['label' => '#', 'title' => Session::t('Realtion Id'), 'class' => 'all'],
    ['label' => Session::t('Consumer Name'), 'class' => 'all'],
    ['label' => Session::t('Con. Id'), 'title' => Session::t('Consumer Id'), 'class' => 'all'],
    ['label' => Session::t('Supplier Name'), 'class' => 'all'],
    ['label' => Session::t('Sup. Id'), 'title' => Session::t('Supplier Id'), 'class' => 'all'],
    ['label' => Session::t('Created')]
];
$table->setTableColumns($columns);

$formatters = [
    ['targets' => 1, 'width' => 2],
    ['targets' => 3, 'width' => 2],
    ['targets' => 5, 'width' => 2],
    ['targets' => 6, 'width' => 15]
];
$table->setFormatters($formatters);

$table->drawTable();

?>
<div class="box-header">
    <h3 class="box-title"><?php echo Session::t('Supplier - Consumer Relations'); ?></h3>
</div>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>
