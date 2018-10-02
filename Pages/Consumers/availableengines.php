<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-qrcode',
    'link' => 'Pages/Consumers/dashboard.php',
    'pagename' => Session::t('Engines')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('Engines'),
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
                <h3 class="box-title"><?php echo Session::t('Available Engines'); ?></h3>
                <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('Engines'), 'Help/enginereports.php'); ?></span>
            </div>
            <div class="box-body">
                <div id='availableengines'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/availableengines.php';
        $("#availableengines").html(loader).load(link);
    });

</script>
