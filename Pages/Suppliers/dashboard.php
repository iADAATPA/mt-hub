<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-dashboard',
    'link' => 'Pages/Suppliers/dashboard.php',
    'pagename' => Session::t('Dashboard')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('Dashboard'),
    'breadcrumbs' => [
       $breadCrumb
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
                        <a href="#engines" data-toggle="tab"><?php echo Session::t('Engines'); ?></a>
                    </li>
                    <li>
                        <a href="#misc" data-toggle="tab"><?php echo Session::t('Metadata'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="engines" class="tab-pane fade in active"></div>
                    <div id="misc" class="tab-pane fade"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Datatables/engines.php';
        $("#engines").html(loader).load(link);

        $('[href="#engines"]').click(function() {
            $("#engines").html(loader).load(link);
        });

        $('[href="#misc"]').click(function(e) {
    		if ($('[href="#misc"]').closest('li').hasClass("disabled")) {
    			e.preventDefault();

    	    	return false;
    	  	} else {
    	  		var link = 'Datatables/metadata.php';
        		$("#misc").html(loader).load(link);
    	  	}
    	});
    });

</script>
