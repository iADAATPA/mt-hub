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
    ['label' => Session::t('Supplier'), 'title' => Session::t('Account Id')],
    ['label' => Session::t('Domains')],
    ['label' => Session::t('TWC'), 'title' => Session::t('Training Word Count')],
    ['label' => Session::t('F-Measure')],
    ['label' => Session::t('BLEU')],
    ['label' => Session::t('TER')],
    ['label' => Session::t('Metadata')]
];

$formatters = [
    ['targets' => 2, 'width' => 1],
    ['targets' => 3, 'width' => 20, 'render' => 'formatAdminEngineName'],
    ['targets' => 4, 'width' => 7, 'render' => 'formatFlag'],
    ['targets' => 5, 'width' => 7, 'render' => 'formatFlag'],
    ['targets' => 6, 'width' => 5],
    ['targets' => 7, 'width' => 5],
    ['targets' => 9, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 10, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 11, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 12, 'width' => 7, 'class' => 'dt-body-right'],
    ['targets' => 13, 'width' => 5, 'render' => 'formatMetaData', 'class' => 'dt-body-center']
];

$table = new DataTables();
$table->setTableId('tableAllEngines');
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/availableengines.php');
$table->setPaging(false);
$table->setSortCol(2);
$table->setServerProcessing(false);
$table->setSortOrder('asc');
$table->setTableColumns($columns);
$table->setEnableBtnDownloadExcel(true);
$table->setFormatters($formatters);
$table->setShowCheckBoxes(true);
$table->setDrawCallback('showPopUps');
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    function formatAdminEngineName(data, type, row) {
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

    function formatMetaData(data, type, row) {
        var table = '<table class=\'table-striped table-popup\'>';
        var tableRows = '';

        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                tableRows += '<tr><td class=\'text-bold\'>' + key + '</td><td>' + data[key].replace(/"/g, "\'");
                +'</td></tr>';
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
