<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-dashboard',
    'link' => 'Pages/Suppliers/domains.php',
    'pagename' => Session::t('Domains')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('Domains'),
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
                <h3 class="box-title"><?php echo Session::t('All Domains'); ?></h3>
                <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('Domains'), 'Help/domains.php'); ?></span>
            </div>
            <div class="box-body">
                <div id='alldomains'></div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/domains.php';
        $("#alldomains").html(loader).load(link);
    });

</script>
