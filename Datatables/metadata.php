<?php

include_once '../functions.php';

Session::authenticateUser();

$engineId = Session::getActiveEngineId();
$engines = new Engines($engineId);
$engineName = $engines->getName();
$src = $engines->getSource();
$trg = $engines->getTarget();

$tableId = 'tableMetadata';
$table = new DataTables();
$table->setTableId($tableId);
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/metadata.php?id=' . $engineId);
$table->setSortCol(2);
$table->setSortOrder('asc');
$table->setPaging(false);
$table->setShowCheckBoxes(true);
$table->setServerProcessing(false);

$columns = [
    ['label' => ''],
    ['label' => '', 'title' => Session::t('Select all'), 'class' => 'all'],
    ['label' => Session::t('Variable'), 'class' => 'all'],
    ['label' => Session::t('Value')]
];
$table->setTableColumns($columns);

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline selectable" id="btnAddMetaData"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('New') . '</button>';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline selectable" id="btnEditMetaData"><i class="fa fa-pencil-square-o fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Edit') . '</button>';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" id="btnDeleteMetaData"><i class="fa fa-trash-o fa-fw fa-lg fa-red" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Delete') . '</button>';
$toolbar .= '</div>';
$table->setCustomToolbar($toolbar);

$table->drawTable();

?>

<div class="box-header">
    <?php Helper::displayEngineName($engineName, $src, $trg); ?>
    <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('Metadata'),
            'Help/metadata.php'); ?></span>
</div>

<div class="box-body" id="dndTranslationData">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $("#btnAddMetaData").click(function () {
            var link = 'Pages/Suppliers/Modals/metadata.php?id=<?php echo Session::getActiveEngineId(); ?>';
            showModal(link, '<?php echo Session::t('Metadata'); ?>');
        });

        $("#btnEditMetaData").click(function () {
            var link = 'Pages/Suppliers/Modals/metadata.php?id=<?php echo Session::getActiveEngineId(); ?>&edit=1';
            showModal(link, '<?php echo Session::t('Metadata'); ?>');
        });

        $("#btnDeleteMetaData").click(function () {
            deleteMetaData();
        });
    });

    function deleteMetaData() {
        var selectedRows = getSelectedCheckboxValues('tableMetadata');
        var message = '';

        if (selectedRows.length === 0) {
            <?php Helper::printWarning(Session::t('Select metadata to deletion.')); ?>
        }

        $('.btn').attr('disabled', 'disabled');
        $('#btnDeleteMetaData').find($('.fa')).removeClass('fa-trash-o fa-red').addClass('fa-refresh fa-spin');

        $.each(selectedRows, function (index, id) {
            $.post(
                "Ajax/metadatadelete.php",
                {
                    engineId: <?php echo $engineId; ?>,
                    id: id,
                    <?php Csrf::printParameters("MetaDataDelete"); ?>
                },
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;
                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        <?php Helper::printSuccess('\' + message + \''); ?>
                        tableMetadata.ajax.reload(null, false);
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                        tableMetadata.ajax.reload(null, false);
                    }
                }
            );
        });

        $('.btn').removeAttr('disabled');
        $("#btnDeleteMetaData").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-trash-o fa-red');
    }

</script>
