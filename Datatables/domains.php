<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => '', 'title' => Session::t('Select all'), 'class' => 'all'],
    ['label' => Session::t('Name')],
    ['label' => Session::t('Source'), 'title' => Session::t('Source Language')],
    ['label' => Session::t('Add Data'), 'title' => Session::t('Add Domain Data'), 'class' => 'all'],
    ['label' => Session::t('Download'), 'title' => Session::t('Downalod Domain Data'), 'class' => 'all'],
    ['label' => Session::t('Delete Data'), 'title' => Session::t('Delete Domain Data'), 'class' => 'all'],
];

$formatters = [
    ['targets' => 2, 'render' => 'formatLongString'],
    ['targets' => 3, 'width' => 10, 'render' => 'formatFlag'],
    ['targets' => 4, 'width' => 1, 'render' => 'formatAddData', 'class' => 'dt-body-center'],
    ['targets' => 5, 'width' => 1, 'render' => 'formatDownloadData', 'class' => 'dt-body-center'],
    ['targets' => 6, 'width' => 1, 'render' => 'formatDeleteData', 'class' => 'dt-body-center']
];

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline selectable" id="btnAddDomain"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Add') . '</button>';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline selectable" id="btnDeleteDomain"><i class="fa fa-trash-o fa-fw fa-lg fa-red" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Delete') . '</button>';
$toolbar .= '</div>';

$tableId = 'tableAllDomains';
$table = new DataTables();
$table->setTableId($tableId);
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/domains.php');
$table->setSortCol(2);
$table->setSortOrder('asc');
$table->setPaging(false);
$table->setShowCheckBoxes(true);
$table->setServerProcessing(false);
$table->setTableColumns($columns);
$table->setFormatters($formatters);
$table->setCustomToolbar($toolbar);
$table->drawTable();

?>

<div class="box-body" id="dndTrainingData">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        $("#btnAddDomain").click(function () {
            var link = 'Pages/Suppliers/Modals/domain.php';
            showModal(link, '<?php echo Session::t('Add Domain'); ?>');
        });

        $("#btnDeleteDomain").click(function () {
            deleteDomain();
        });
    });

    function formatAddData(data, type, row) {
        var icon = '<span title=\"<?php echo Session::t('Data added. To add a new data please delete first current data.'); ?>\" ><i class=\"fa fa-lg fa-upload fa-green fa-pointer fa-disabled\" aria-hidden=\"true\"></i></span>';

        if (row[4] == 0) {
            icon = '<span title=\"<?php echo Session::t('Add Domain Data'); ?>\" onClick=\"addDomainData(' + row[0] + ')\"><i class=\"fa fa-lg fa-upload fa-green fa-pointer\" aria-hidden=\"true\"></i></span>';
        }

        return icon;
    }

    function formatDownloadData(data, type, row) {
        var icon = '<span title=\"<?php echo Session::t('No Domain Data to downalod'); ?>\" ><i class=\"fa fa-lg fa-download fa-pointer fa-disabled\" aria-hidden=\"true\"></i></span>';

        if (row[5] > 1) {
            icon = '<a href=\"download.php?id=' + row[5] + '\" target=\"_blank\"><span title=\"<?php echo Session::t('Download Domain Data'); ?>\"><i class=\"fa fa-lg fa-download fa-pointer\" aria-hidden=\"true\"></i></span></a>';
        }

        return icon;
    }

    function formatDeleteData(data, type, row) {
        var icon = '<span title=\"<?php echo Session::t('No Domain Data to delete'); ?>\" ><i class=\"fa fa-lg fa-ban fa-red fa-pointer fa-disabled\" aria-hidden=\"true\"></i></span>';

        if (row[4] == 1) {
            icon = '<span title=\"<?php echo Session::t('Delete Domain Data'); ?>\" onClick=\"deleteDomainData(' + row[0] + ')\"><i class=\"fa fa-lg fa-ban fa-red fa-pointer\" aria-hidden=\"true\"></i></span>';
        }

        return icon;
    }

    function formatDomainEditProperties(data, type, row, accountId) {
        var icon = '<span title=\"<?php echo Session::t('Edit Domain'); ?>\" onClick=\"editDomain(' + row[0] + ')\"><i class=\"fa fa-lg fa-pencil-square-o fa-pointer\" aria-hidden=\"true\"></i></span>';

        if (row[5] != accountId) {
            icon = '<span title=\"<?php echo Session::t('Edit Domain not available for a general domains'); ?>\" ><i class=\"fa fa-lg fa-pencil-square-o fa-disabled\" aria-hidden=\"true\"></i></span>';
        }

        return icon;
    }

    function deleteDomain() {
        var selectedRows = getSelectedCheckboxValues('tableAllDomains');
        var message = '';

        if (selectedRows.length === 0) {
            selectedRows.push(<?php echo Session::getActiveDomainId(); ?>);
        }

        $('.btn').attr('disabled', 'disabled');

        $('#btnDeleteDomain').find($('.fa')).removeClass('fa-trash-o fa-red').addClass('fa-refresh fa-spin');

        // Iterate over all selected checkboxes
        $.each(selectedRows, function (index, domainId) {
            $.post(
                "Ajax/domaindelete.php",
                {
                    id: domainId,
                    <?php Csrf::printParameters("DeleteDomain"); ?>
                },
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;
                    if (window.tableAllDomains != undefined) {
                        tableAllDomains.ajax.reload();
                    }

                    if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                        <?php Helper::printSuccess('\' + message + \''); ?>
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                    }
                }
            );
        });

        $('.btn').removeAttr('disabled');
        $("#btnDeleteDomain").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-trash-o fa-red');
    }

    function downloadDomainData(id) {
        $.post(
            "Ajax/domaindatadownload.php",
            {
                id: id,
                <?php Csrf::printParameters("DownloadDomainData"); ?>
            },
            function (response) {
                response = JSON.parse(response);
                var message = response.message;
                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    download(message, response.data + ".txt");
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    }

    function deleteDomainData(id) {
        $.post(
            "Ajax/domaindatadelete.php",
            {
                id: id,
                <?php Csrf::printParameters("DeleteDomainData"); ?>
            },
            function (response) {
                response = JSON.parse(response);
                var message = response.message;
                if (window.tableAllDomains != undefined) {
                    tableAllDomains.ajax.reload();
                }

                if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    }

    function download(dataurl, filename) {
        var a = document.createElement("a");
        a.href = dataurl;
        a.setAttribute("download", filename);
        var b = document.createEvent("MouseEvents");
        b.initEvent("click", false, true);
        a.dispatchEvent(b);
        return false;
    }

    function addDomainData(id) {
        var link = 'Pages/Suppliers/Modals/domaindata.php?id=' + id;
        showModal(link, '<?php echo Session::t('Add Domain Data'); ?>');
    }

</script>
