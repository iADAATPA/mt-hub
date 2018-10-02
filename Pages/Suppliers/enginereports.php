<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-dashboard',
    'link' => 'Pages/Suppliers/enginereports.php',
    'pagename' => Session::t('Engine Reports')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('Engine Reports'),
    'breadcrumbs' => [
        $breadCrumb
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('All Engines'); ?></h3>
                <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('All Engines'), 'Help/engines.php'); ?></span>
            </div>
            <div class="box-body">
                <div id='engineReports'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/enginereports.php';
        $("#engineReports").html(loader).load(link);
    });

</script>
