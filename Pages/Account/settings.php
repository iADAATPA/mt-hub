<?php

include_once '../../functions.php';

Session::authenticateUser();

$header = [
    'title' => Session::t('Settings'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-user-circle-o',
            'link' => 'Pages/Account/settings.php',
            'pagename' => Session::t('Settings')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="settingsTabs">
                <li class="active">
                    <a href="#user" data-toggle="tab"><?php echo Session::t('User'); ?></a>
                </li>
				 <li>
                    <a href="#account" data-toggle="tab"><?php echo Session::t('Account'); ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="user" class="tab-pane fade in active"></div>
                <div id="account" class="tab-pane fade"></div>
                <?php if (in_array(Session::getGroupId(), [Groups::GROUP_SUPPLIER, Groups::GROUP_CONSUMER])) { ?>
                <div id="usage" class="tab-pane fade"></div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(document).ready(function() {
		var link = 'Pages/Account/user.php';
        $("#user").html(loader).load(link);

        $('[href="#user"]').click(function() {
    		var link = 'Pages/Account/user.php';
            $("#user").html(loader).load(link);
        });

        $('[href="#account"]').click(function(e) {
    		if ($('[href="#account"]').closest('li').hasClass("disabled")) {
    			e.preventDefault();

    	    	return false;
    	  	}
    	  	else {
    	  		var link = 'Pages/Account/account.php';
        		$("#account").html(loader).load(link);
    	  	}
    	});

        $('[href="#users"]').click(function(e) {
    		if ($('[href="#users"]').closest('li').hasClass("disabled")) {
    			e.preventDefault();

    	    	return false;
    	  	}
    	  	else {
        		var link = 'Datatables/accountusers.php';
        		$("#users").html(loader).load(link);
    	  	}
    	});
    });

</script>
