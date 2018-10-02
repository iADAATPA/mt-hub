<?php

include_once '../../functions.php';

Session::authenticateUser();

$header = [
    'title' => Session::t('Activity Log'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-list-ol',
            'link' => 'Pages/Suppliers/supplierlogs.php',
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

    $(document).ready(function() {
        var link = 'Datatables/supplierlogs.php';
        $("#logs").html(loader).load(link);
    });

</script>
