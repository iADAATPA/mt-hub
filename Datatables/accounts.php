<?php

include_once '../functions.php';

Session::authenticateUser();

$table = new DataTables();
$table->setTableId('tableAllAccounts');
$table->setEnableBtnDownloadExcel(true);
$table->setAjaxCallBack('Datatables/Ajax/accounts.php');
$table->setSortCol(1);
$table->setSortOrder('desc');
$table->setLengthChange(false);
$table->setPaging(false);

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" title="Create new account" id="buttonCreateAccount"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('New Account') . '</button>';
$toolbar .= '<button id="filterAllAccounts" type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Show all Accounts') . '"><i class="fa fa-th-list fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('All') . '</button>';
$toolbar .= '<button id="filterSupplierAccounts" type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Show Supplier Accounts') . '"><i class="fa fa-id-card-o fa-amber fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Suppliers') . '</button>';
$toolbar .= '<button id="filterConsumerAccounts" type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Show Consumer Accounts') . '"><i class="fa fa-handshake-o fa-palegray fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('Consumers') . '</button>';
$toolbar .= '</div>';
$table->setCustomToolbar($toolbar);

$filters = [
    [
        'buttonId' => 'filterAllAccounts',
    ],
    [
        'buttonId' => 'filterSupplierAccounts',
        'column' => 10,
        'search' => [
            'input' => Groups::GROUP_SUPPLIER,
            'regex' => false,
            'smart' => false
        ],
    ],
    [
        'buttonId' => 'filterConsumerAccounts',
        'column' => 10,
        'search' => [
            'input' => Groups::GROUP_CONSUMER,
            'regex' => false,
            'smart' => false
        ],
    ]
];
$table->setFilters($filters);

$columns = [
    ['label' => ''],
    ['label' => '#', 'title' => Session::t('Account Id'), 'class' => 'all'],
    ['label' => Session::t('Status'), 'class' => 'all'],
    ['label' => Session::t('Name'), 'class' => 'all'],
    ['label' => Session::t('Account Type')],
    ['label' => Session::t('Admin Id')],
    ['label' => Session::t('Created')],
    ['label' => '', 'class' => 'all'],
    ['label' => '', 'class' => 'all'],
    ['label' => ''],
    ['label' => ''],
];
$table->setTableColumns($columns);

$formatters = [
    ['targets' => 1, 'width' => 2],
    ['targets' => 2, 'width' => 1, 'render' => 'formatAccountStatus', 'class' => 'dt-body-center'],
    ['targets' => 3, 'render' => 'formatLongString'],
    ['targets' => 5, 'width' => 10],
    ['targets' => 6, 'width' => 20],
    ['targets' => 7, 'width' => 1, 'class' => 'dt-body-center', 'orderable' => 'false'],
    ['targets' => 8, 'width' => 1, 'class' => 'dt-body-center', 'orderable' => 'false'],
    ['targets' => 9, 'visible' => 'false'],
    ['targets' => 10, 'visible' => 'false']
];
$table->setFormatters($formatters);

$table->drawTable();

?>
<div class="box-header">
    <h3 class="box-title"><?php echo Session::t('All Accounts'); ?></h3>
</div>

<div class="box-body">
    <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        $('#buttonCreateAccount').click(function () {
            var link = 'Pages/ControlPanel/Modals/account.php';
            showModal(link, '<?php echo Session::t('New Account'); ?>');
        });
    });

    function loginToAccount(id) {
        $.post(
            "Ajax/accountswitch.php",
            {
                id: id,
                <?php Csrf::printParameters("AccountSwitch"); ?>
            },
            function (response) {
                response = JSON.parse(response);
                var message = response.message;
                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    location.reload();
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    }

    function editAccount(id) {
        var link = 'Pages/ControlPanel/Modals/account.php?id=' + id;
        showModal(link, '<?php echo Session::t('Edit account'); ?>');
    }

</script>
