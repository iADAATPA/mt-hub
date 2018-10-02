<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$header = [
    'title' => Session::t('Request Log'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-list-ul',
            'link' => 'Pages/ControlPanel/requestlogs.php',
            'pagename' => Session::t('Request Log')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('Requests'); ?></h3>
             </div>
            <div class="box-body">
                <div id='requestlogs'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/requestlogs.php';
        $("#requestlogs").html(loader).load(link);
    });

</script>
