<?php

include_once '../functions.php';

Session::authenticateUser();

$engineId = Session::getActiveEngineId();

$engines = new Engines($engineId);
$engineName = $engines->getName();
$src = $engines->getSource();
$trg = $engines->getTarget();

$columns = [
    ['label' => ''],
    ['label' => '#', 'title' => Session::t('Engine Id'), 'class' => 'all'],
    ['label' => Session::t('Engine'), 'title' => Session::t('Engine Name'), 'class' => 'all'],
    ['label' => Session::t('Source'), 'title' => Session::t('Source Language')],
    ['label' => Session::t('Target'), 'title' => Session::t('Target Language')],
    ['label' => Session::t('Domains')],
    ['label' => Session::t('TWC'), 'title' => Session::t('Training Word Count')],
    ['label' => Session::t('F-Measure')],
    ['label' => Session::t('BLEU')],
    ['label' => Session::t('TER')],
];

$formatters = [
    ['targets' => 1, 'width' => 1],
    ['targets' => 2, 'width' => 20, 'render' => 'formatEngineName'],
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
$table->setAjaxCallBack('Datatables/Ajax/metadataconsumers.php');
$table->setPaging(false);
$table->setSortCol(2);
$table->setServerProcessing(false);
$table->setSortOrder('asc');
$table->setTableColumns($columns);
$table->setEnableBtnDownloadExcel(true);
$table->setFormatters($formatters);
$table->drawTable();

?>

<div class="box-header">
    <h3 class="box-title"><?php echo Session::t('All Engines'); ?></h3>
    <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('All Engines'),
            'Help/enginereports.php'); ?></span>
</div>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        window.activeEngineId = '<?php echo $engineId ? $engineId : 0; ?>';
        window.activeEngineName = '<?php echo $engineName ? $engineName : ''; ?>';

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

        $("#btnAddEngine").click(function () {
            var link = 'Pages/Suppliers/Modals/engine.php';
            showModal(link, '<?php echo Session::t('Add New Engine'); ?>');
        });

        $("#btnCopyEngine").click(function () {
            copyEngine();
        });

        $("#btnDeleteEngine").click(function () {
            deleteEngine();
        });
    });

    function formatEngineName(data, type, row) {
        var name = row[2];
        title = '<?php echo Session::t('Click here to select the engine as an active.'); ?>';
        var online = row[5];

        if (name.length > 60) {
            name = name.substring(0, 57) + '...';
        }

        if (online != 0) {
            name = '<span class=\"offline\">' + name + '</span>';
        }

        return '<span style=\"width: 100%;  cursor:pointer;\" title=\"' + title + '\">' + name + '</span>';
    }

</script>
