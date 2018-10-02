<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$header = [
    'title' => Session::t('Accounts') . '/' . Session::t('Users'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-address-card',
            'link' => 'Pages/ControlPanel/accounts.php',
            'pagename' => Session::t('Accounts') . '/' . Session::t('Users')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class"col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="dashboardTabs">
                    <li class="active">
                        <a href="#accounts" data-toggle="tab"><?php echo Session::t('Accounts'); ?></a>
                    </li>
                    <li>
                        <a href="#users" data-toggle="tab"><?php echo Session::t('Users'); ?></a>
                    </li>
                    <li>
                        <a href="#relations" data-toggle="tab"><?php echo Session::t('Relations'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="accounts" class="tab-pane fade in active">
                    </div>
                    <div id="users" class="tab-pane fade">
                    </div>
                    <div id="relations" class="tab-pane fade">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/accounts.php';
        $("#accounts").html(loader).load(link);

        $('[href="#accounts"]').click(function() {
            var link = 'Datatables/accounts.php';
            $("#accounts").html(loader).load(link);
        });

        $('[href="#users"]').click(function() {
            var link = 'Datatables/users.php';
            $("#users").html(loader).load(link);
        });

        $('[href="#relations"]').click(function() {
            var link = 'Datatables/relations.php';
            $("#relations").html(loader).load(link);
        });
    });

</script>
