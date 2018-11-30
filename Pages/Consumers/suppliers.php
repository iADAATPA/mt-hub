<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-qrcode',
    'link' => 'Pages/Consumers/dashboard.php',
    'pagename' => Session::t('Suppliers')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('Suppliers'),
    'breadcrumbs' => [
        $breadCrumb
    ],
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('Available Suppliers'); ?></h3>
                <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('Relations'), 'Help/relations.php'); ?></span>
            </div>
            <div class="box-body">
                <div id='suppliers'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/suppliers.php';
        $("#suppliers").html(loader).load(link);
    });

</script>
