<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$header = [
    'title' => Session::t('Engines'),
    'breadcrumbs' => [
        [
            'icon' => 'a-th-list',
            'link' => 'Pages/ControlPanel/engines.php',
            'pagename' => Session::t('Engines')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('All Engines Report'); ?></h3>
                <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('All Engines Report'), 'Help/enginereports.php'); ?></span>
            </div>
            <div class="box-body">
                <div id='allEngines'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/allengines.php';
        $("#allEngines").html(loader).load(link);
    });

</script>
