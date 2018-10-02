<?php

include_once '../functions.php';

Session::authenticateUser();

$columns = [
    ['label' => ''],
    ['label' => ''],
    ['label' => Session::t('Status'), 'class' => 'all'],
    ['label' => Session::t('Account Name'), 'class' => 'all'],
    ['label' => Session::t('Administrator Email')],
    ['label' => Session::t('Administrator Name')],
    ['label' => Session::t('Added')],
    ['label' => Session::t(''), 'title' => Session::t('Edit properties'), 'class' => 'all']
];

$formatters = [
    ['targets' => 0, 'visible' => 'false'],
    ['targets' => 1, 'visible' => 'false'],
    ['targets' => 2, 'width' => 1, 'render' => 'formatAccountStatus', 'class' => 'dt-body-center'],
    ['targets' => 3, 'render'=> 'formatLongString', 'class' => 'dt-body-left'],
    ['targets' => 4, 'width' => 25, 'render' => 'formatEmail'],
    ['targets' => 5, 'width' => 25, 'render'=> 'formatLongString', 'class' => 'dt-body-left'],
    ['targets' => 6, 'width' => 15],
    ['targets' => 7, 'width' => 1, 'render'=> 'formatConsumerEditProperties', 'orderable'=> 'false', 'class' => 'dt-body-center']
];

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline selectable" title="' . Session::t('New Supplier') . '" id="btnAddSupplier"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('New') . '</button>';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Delete Supplier') . '" id="btnDeleteSupplier"><i class="fa fa-trash-o fa-fw fa-lg fa-red" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Delete') . '</button>';
$toolbar .= '</div>';

$table = new DataTables();
$table->setTableId('tableSuppliers');
$table->setLengthChange(false);
$table->setAjaxCallBack('Datatables/Ajax/suppliers.php');
$table->setPaging(false);
$table->setShowCheckBoxes(true);
$table->setSortCol(2);
$table->setEnableBtnDownloadExcel(true);
$table->setSortOrder('asc');
$table->setTableColumns($columns);
$table->setFormatters($formatters);
$table->setCustomToolbar($toolbar);
$table->drawTable();

?>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $("#btnAddSupplier").click(function () {
            var link = 'Pages/Consumers/Modals/supplier.php';
            showModal(link, '<?php echo Session::t('New Supplier'); ?>');
        });

        $("#btnDeleteSupplier").click(function () {
            $('.btn').attr('disabled', 'disabled');
            $('#btnDeleteSupplier').find($('.fa')).removeClass('fa-trash-o fa-red').addClass('fa-refresh fa-spin');

            var selectedRows = getSelectedCheckboxValues('tableSuppliers');

            $.each(selectedRows, function (index, id) {
                $.post(
                    "Ajax/consumerdelete.php",
                    {
                        id: id,
                        <?php Csrf::printParameters("DeleteSupplier"); ?>
                    },
                    function (response) {
                        response = JSON.parse(response);
                        var message = response.message;

                        if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                            <?php Helper::printSuccess('\' + message + \''); ?>
                            $("#btnAddSupplier").prop("disabled", false);
                            tableSuppliers.ajax.reload(null, false);
                        } else {
                            <?php Helper::printError('\' + message + \''); ?>
                            tableSuppliers.ajax.reload(null, false);
                        }
                    }
                );
            });

            $('.btn').removeAttr('disabled');
            $("#btnDeleteSupplier").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-trash-o fa-red');
        });
    });

</script>
