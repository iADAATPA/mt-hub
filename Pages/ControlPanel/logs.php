<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$header = [
    'title' => Session::t('Activity Log'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-list-ol',
            'link' => 'Pages/ControlPanel/logs.php',
            'pagename' => Session::t('Activity Log')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('Activities'); ?></h3>
             </div>
            <div class="box-body">
                <div id='logs'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/logs.php';
        $("#logs").html(loader).load(link);
    });

</script>
