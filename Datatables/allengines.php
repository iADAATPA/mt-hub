<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => ''],
    ['label' => '#', 'title' => Session::t('Engine Id'), 'class' => 'all'],
    ['label' => Session::t('Engine'), 'title' => Session::t('Engine Name'), 'class' => 'all'],
    ['label' => Session::t('Source'), 'title' => Session::t('Source Language')],
    ['label' => Session::t('Target'), 'title' => Session::t('Target Language')],
    ['label' => Session::t('Type'), 'title' => Session::t('Engine Type')],
    ['label' => Session::t('Acc. Id'), 'title' => Session::t('Account Id')],
    ['label' => Session::t('Domain')],
    ['label' => Session::t('TWC'), 'title' => Session::t('Training Word Count')],
    ['label' => Session::t('F-Measure')],
    ['label' => Session::t('BLEU')],
    ['label' => Session::t('TER')],
    ['label' => Session::t('Metadata')],
    ['label' => Session::t('&nbsp;'), 'title' => Session::t('Edit properties'), 'class' => 'all']
];

$formatters = [
    ['targets' => 2, 'width' => 1],
    ['targets' => 3, 'width' => 20, 'render'=> 'formatAdminEngineName'],
    ['targets' => 4, 'width' => 7, 'render'=> 'formatFlag'],
    ['targets' => 5, 'width' => 7, 'render'=> 'formatFlag'],
    ['targets' => 9, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 10, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 11, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 12, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 13, 'width' => 5, 'render'=> 'formatMetaData', 'class' => 'dt-body-center'],
    ['targets' => 14, 'width' => 1, 'render'=> 'formatEditAdminProperties', 'orderable'=> 'false', 'class' => 'dt-body-center']
];

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Copy engine') . '" id="btnAdminCopyEngine"><i class="fa fa-files-o fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Copy') . '</button>';
$toolbar .= '</div>';

$table = new DataTables();
$table->setTableId('tableAllEngines');
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/allengines.php');
$table->setPaging(false);
$table->setSortCol(2);
$table->setServerProcessing(false);
$table->setSortOrder('asc');
$table->setTableColumns($columns);
$table->setEnableBtnDownloadExcel(true);
$table->setFormatters($formatters);
$table->setShowCheckBoxes(true);
$table->setDrawCallback('showPopUps');
$table->setCustomToolbar($toolbar);
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function() {

        $("#btnAdminAddEngine").click(function () {
            var link = 'Pages/Suppliers/Modals/engine.php?admin=1';
            showModal(link, '<?php echo Session::t('Add New Engine'); ?>');
        });

        $("#btnAdminCopyEngine").click(function () {
           adminCopyEngine();
        });

        $("#btnAdminDeleteEngine").click(function () {
         //   adminDeleteEngine();
        });
    });

    function formatAdminEngineName(data, type, row){
        var name = row[3];
        var nameDisplay = name;
        var title = row[3];
        var online = row[14];

        if (name.length > 60) {
            name = name.substring(0, 57) + '...';
        }

        if (online == 0) {
            nameDisplay = '<span class=\"offline\">' + name + '</span>';
        }

        return '<span style=\"width: 100%;  cursor:pointer;\" title=\"' + title + '\">' + nameDisplay + '</span>';
    }

    function adminCopyEngine() {
        var selectedRows = getSelectedCheckboxValues('tableAllEngines');

        if (selectedRows.length === 0) {
            <?php Helper::printWarning(Session::t('Please select engines you want to copy.')); ?>
        } else {
            var link = 'Pages/ControlPanel/Modals/enginecopy.php?engines=' + selectedRows;
            showModal(link, '<?php echo Session::t('Copy Engines'); ?>');
        }
    }

    function formatEditAdminProperties(data, type, row) {
        var icon = '<span id=\"btnEdit_' + row[0] + '\" title=\"<?php echo Session::t('Edit properties'); ?>\" onClick=\"adminEditEngine(' + row[0] + ')\"><i class=\"fa fa-lg fa-pencil-square-o fa-pointer\" aria-hidden=\"true\"></i></span>';

        return icon;
    }

    function adminEditEngine(id) {
        var link = 'Pages/Suppliers/Modals/engine.php?admin=1&id=' + id;
        showModal(link, '<?php echo Session::t('Edit properties'); ?>');
    }

    function formatMetaData(data, type, row) {
        var table = '<table class=\'table-striped table-popup\'>';
        var tableRows = '';

        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                tableRows += '<tr><td class=\'text-bold\'>' + key + '</td><td>' + data[key].replace(/"/g, "\'") +'</td></tr>';
            }
        }

        if (tableRows == '') {
            tableRows = '<tr><td><?php echo Session::t('Metadata not specified'); ?><td></tr>';
        }

        table = table + tableRows + '</table>';

        var metadata = '<span class="popoverButton" type="button" data-toggle="popover" data-trigger="hover" ' +
            'data-container="body" title="<?php echo Session::t('Metadata'); ?>" data-content="' + table + '" data-placement="auto"> ' +
            '<i class="fa fa-fw fa-lg fa-info-circle fa-pointer fa-deeporange"></i></span>';

        return metadata;
    }

</script>
