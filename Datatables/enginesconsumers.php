<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => ''],
    ['label' => Session::t('Engine'), 'title' => Session::t('Engine Name'), 'class' => 'all'],
    ['label' => Session::t('Source'), 'title' => Session::t('Source Language')],
    ['label' => Session::t('Target'), 'title' => Session::t('Target Language')],
    ['label' => ''],
    ['label' => ''],
    ['label' => Session::t('Domain')]
];

$formatters = [
    ['targets' => 2, 'width' => 60, 'render' => 'formatEngineName'],
    ['targets' => 3, 'width' => 10, 'render' => 'formatFlag'],
    ['targets' => 4, 'width' => 10, 'render' => 'formatFlag'],
    ['targets' => 5, 'visible' => 'false'],
    ['targets' => 6, 'visible' => 'false']
];

$table = new DataTables();
$table->setTableId('tableEnginesConsumer');
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/enginesconsumer.php');
$table->setPaging(false);
$table->setSortCol(2);
$table->setServerProcessing(false);
$table->setShowCheckBoxes(true);
$table->setEnableBtnDownloadExcel(true);
$table->setSortOrder('asc');
$table->setTableColumns($columns);
$table->setFormatters($formatters);
$table->drawTable();

?>

<div class="box-header">
    <?php Helper::displaySupplier(Session::getActiveSupplierName()); ?>
</div>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $(document).on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $(document).on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $(document).on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
    });

    function formatEngineName(data, type, row) {
        var name = row[2];
        var nameDisplay = name;
        var title = row[2];
        var online = row[5];

        if (name.length > 60) {
            name = name.substring(0, 57) + '...';
        }

        if (online == 0) {
            nameDisplay = '<span class=\"offline\">' + name + '</span>';
        }

        return '<span style=\"width: 100%;\"  title=\"' + title + '\">' + nameDisplay + '</span>';
    }

</script>
