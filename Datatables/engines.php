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
    ['label' => '', 'title' => Session::t('Select all'), 'class' => 'all'],
    ['label' => Session::t('Engine'), 'title' => Session::t('Engine name'), 'class' => 'all'],
    ['label' => Session::t('Domain')],
    ['label' => Session::t('Custom Id'), 'class' => 'all'],
    ['label' => Session::t('Type'), 'class' => 'all'],
    ['label' => Session::t('Source'), 'title' => Session::t('Source Language')],
    ['label' => Session::t('Target'), 'title' => Session::t('Target Language')],
    ['label' => Session::t(''), 'title' => Session::t('Edit properties'), 'class' => 'all']
];

$formatters = [
    ['targets' => 2, 'width' => 30, 'render'=> 'formatEngineName'],
    ['targets' => 3, 'width' => 15, 'render'=> 'formatLongString'],
    ['targets' => 4, 'width' => 15, 'render'=> 'formatLongString'],
    ['targets' => 5, 'width' => 5],
    ['targets' => 6, 'width' => 10, 'render'=> 'formatFlag'],
    ['targets' => 7, 'width' => 10, 'render'=> 'formatFlag'],
    ['targets' => 8, 'width' => 1, 'render'=> 'formatEditProperties', 'orderable'=> 'false', 'class' => 'dt-body-center'],
    ['targets' => 9, 'visible'=> 'false'],
    ['targets' => 10, 'visible'=> 'false']
];

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline selectable" title="' . Session::t('Add New Engine') . '" id="btnAddEngine"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('New') . '</button>';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Delete engine') . '" id="btnDeleteEngine"><i class="fa fa-trash-o fa-fw fa-lg fa-red" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Delete') . '</button>';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" title="'. Session::t('Copy engine') . '" id="btnCopyEngine"><i class="fa fa-files-o fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Copy') . '</button>';
$toolbar .= '</div>';

$table = new DataTables();
$table->setTableId('tableEngines');
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/engines.php');
$table->setPaging(false);
$table->setSortCol(2);
$table->setSortOrder('asc');
$table->setShowCheckBoxes(true);
$table->setTableColumns($columns);
$table->setCustomToolbar($toolbar);
$table->setServerProcessing(false);
$table->setFormatters($formatters);
$table->drawTable();

?>
<div class="box-header">
	<?php Helper::displayEngineName($engineName, $src, $trg); ?>
    <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('Engines'), 'Help/engines.php'); ?></span>
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
        var nameDisplay = name;
        var title = row[2];
        title = '<?php echo Session::t('Click here to select the engine as an active.'); ?>';
        var engineId = row[1];
        var activeEngine = row[10];
        var online = row[8];

        if (name.length > 60) {
            name = name.substring(0, 57) + '...';
        }

        if (online == 0) {
            nameDisplay = '<span class=\"offline\">' + name + '</span>';
        }

        if (activeEngine) {
            var src = row[6];
            var trg = row[7];
            $('.activeEngineSrc').text(src);
            $('.activeEngineTrg').text(trg);
            $('#activeEngineSrcFlag').attr('class', 'font-xsmall flag-icon flag-icon-' + src);
            $('#activeEngineTrgFlag').attr('class', 'font-xsmall flag-icon flag-icon-' + trg);

            activeEngineId = row[1];
            activeEngineName = row[2];

            $('.activeEngineName').text(name);

            return '<span style=\"width: 100%; font-weight: bold; cursor:pointer;\" onClick=\"changeEngine(' + engineId + ', \'' + row[2] + '\')\" title=\"' + title + '\">' + nameDisplay + ' [Active]' + '</span>';
        } else {
            return '<span style=\"width: 100%;  cursor:pointer;\" onClick=\"changeEngine(' + engineId + ', \'' + row[2] + '\')\" title=\"' + title + '\">' + nameDisplay + '</span>';
        }
    }

    function copyEngine() {
        var selectedRows = getSelectedCheckboxValues('tableEngines');
        var message = '';

        $('.btn').attr('disabled', 'disabled');
        $('#btnCopyEngine').find($('.fa')).removeClass('fa-files-o fa-palegray').addClass('fa-refresh fa-spin');

        if (selectedRows.length === 0) {
            selectedRows.push(<?php echo Session::getActiveEngineId(); ?>);
        }

        $.each(selectedRows, function (index, id) {
            $.post(
                "Ajax/enginecopy.php",
                {
                    id: id,
                    <?php Csrf::printParameters("CopyEngine"); ?>
                },
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;
                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        <?php Helper::printSuccess('\' + message + \''); ?>
                        tableEngines.ajax.reload(null, false);
                        $('.btn').removeAttr('disabled');
                        $("#btnCopyEngine").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-files-o fa-palegray');
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                        tableEngines.ajax.reload(null, false);
                        $('.btn').removeAttr('disabled');
                        $("#btnCopyEngine").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-files-o fa-palegray');
                    }
                }
            );
        });

        $('.btn').removeAttr('disabled');
        $("#btnCopyEngine").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-files-o fa-palegray');
    }

    function deleteEngine() {
        var selectedRows = getSelectedCheckboxValues('tableEngines');

        if (selectedRows.length === 0) {
            selectedRows.push(<?php echo Session::getActiveEngineId(); ?>);
        }

        $('.btn').attr('disabled', 'disabled');
        $('#btnDeleteEngine').find($('.fa')).removeClass('fa-trash-o fa-red').addClass('fa-refresh fa-spin');

        $.each(selectedRows, function (index, engineId) {
            $.post(
                "Ajax/enginedelete.php",
                {
                    id: engineId,
                    <?php Csrf::printParameters("DeleteEngine"); ?>
                },
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;
                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        <?php Helper::printSuccess('\' + message + \''); ?>
                        tableEngines.ajax.reload(null, false);
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                        tableEngines.ajax.reload(null, false);
                    }
                }
            );
        });

        $('.btn').removeAttr('disabled');
        $("#btnDeleteEngine").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-trash-o fa-red');
    }

    function changeEngine(engineId, engineName) {
        $.post(
            "Ajax/enginechange.php",
            {
                id: engineId,
                <?php Csrf::printParameters("ChangeEngine"); ?>
            },
            function (result) {
                window.activeEngineId = engineId;
                window.activeEngineName = engineName;

                tableEngines.ajax.reload(null, false);
            }
        );
    }

    function formatEditProperties(data, type, row) {
        var icon = '<span id=\"btnEdit_' + row[0] + '\" title=\"<?php echo Session::t('Edit properties'); ?>\" onClick=\"editEngine(' + row[0] + ')\"><i class=\"fa fa-lg fa-pencil-square-o fa-pointer\" aria-hidden=\"true\"></i></span>';

        return icon;
    }

</script>
