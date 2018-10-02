<?php

include_once '../functions.php';

Session::authenticateUser();

$table = new DataTables();
$table->setTableId('tableAllUsers');
$table->setEnableBtnDownloadExcel(true);
$table->setAjaxCallBack('Datatables/Ajax/users.php');
$table->setSortCol(1);
$table->setSortOrder('desc');
$table->setLengthChange(false);
$table->setPaging(false);

$columns = [
    ['label' => ''],
    ['label' => '#', 'title' => Session::t('User Id'), 'class' => 'all'],
    ['label' => Session::t('Name'), 'class' => 'all'],
    ['label' => Session::t('Email')],
    ['label' => Session::t('Created')],
    ['label' => Session::t('Last Login')],
    ['label' => Session::t('Account Id')],
    ['label' => '', 'class' => 'all'],
    ['label' => '', 'class' => 'all'],
    ['label' => '', 'class' => 'all']
];
$table->setTableColumns($columns);

$toolbar = '<div class="btn-group">';
$toolbar .= '<button type="button" class="btn btn-sm btn-info outline" title="' . Session::t('Create New User') . '" id="buttonCreateUser"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i>&nbsp;&nbsp;' . Session::t('New User') . '</button>';
$toolbar .= '</div>';
$table->setCustomToolbar($toolbar);

$formatters = [
    ['targets' => 1, 'width' => 1, 'class' => 'dt-body-right'],
    ['targets' => 3, 'render' => 'formatLongString'],
    ['targets' => 3, 'render' => 'formatEmail'],
    ['targets' => 4, 'width' => 15],
    ['targets' => 5, 'width' => 15],
    ['targets' => 6, 'width' => 10],
    ['targets' => 7, 'width' => 1, 'render' => 'formatEditIcon', 'class' => 'dt-body-center', 'orderable' => 'false'],
    ['targets' => 8, 'width' => 1, 'render' => 'formatLoginAsIcon', 'class' => 'dt-body-center', 'orderable' => 'false'],
    ['targets' => 9, 'width' => 1, 'render' => 'formatResendPasswordIcon', 'class' => 'dt-body-center', 'orderable' => 'false']
];
$table->setFormatters($formatters);

$table->drawTable();

?>

<div class="box-header">
	<h3 class="box-title"><?php echo Session::t('Users Management'); ?></h3>
</div>

<div class="box-body">
   <?php $table->printTable(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $("#buttonCreateUser").click(function () {
            var link = 'Pages/ControlPanel/Modals/user.php';
            showModal(link, '<?php echo Session::t('New User'); ?>');
        });
    });

	function formatEmail(data, type, row) {
		var email = row[7];

		if (email != null && email.length > 31) {
			email = email.substring(0, 27) + "...";
		};
		
		var mailImg = '<i class="fa fa-lg fa-envelope-o fa-fw fa-deeporange fa-pointer" aria-hidden="true"></i>';
		var link = "<span title=\"" + row[7] + "\">" + "<a  href=\'mailto:" + row[7] + "\'>" + mailImg + "</a>&nbsp;&nbsp;" + email + "</span>";

		return link;
	}

	function formatEditIcon(data, type, row) {
		var icon = '<span id=\"btnEdit_' + row[0] + '\" title=\"<?php echo Session::t('Edit properties'); ?>\" onClick=\"editUser(' + row[0] + ')\"><i class=\"fa fa-lg fa-pencil-square-o fa-pointer\" aria-hidden=\"true\"></i></span>';

	   	return icon;
	}

	function formatLoginAsIcon(data, type, row) {
		var icon = '<span title=\"<?php echo Session::t('Login as a User'); ?>\" onClick=\"loginToAccount(' + row[6] + ',' + row[0] + ')\"><i class=\"fa fa-lg fa-sign-in fa-green fa-pointer\" aria-hidden=\"true\"></i></a>';

	   	return icon;
	}

	function formatResendPasswordIcon(data, type, row) {
		var icon = '<i onclick="resendPass(\'' + row[2] + '\')" title=\"<?php echo Session::t('Resend set password email'); ?>\" class=\"fa fa-lg fa-ticket fa-red fa-pointer\" aria-hidden=\"true\"></i>';

	   	return icon;
	}

	function resendPass(name) {
		dialogConfirm('<?php echo Session::t("Are you sure you want to resend the \'Set Password\' email?"); ?>', '<?php echo Session::t('Passowrd Email'); ?>', function(response) {
			if (response == true) {
                $.post(
                    'Ajax/userforgotpassword.php',
                    {
                        name: name
                    },
                    function(response) {
                        response = JSON.parse(response);
                        var message = response.message;
                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            <?php Helper::printSuccess('\' + message + \''); ?>
                        } else {
                            <?php Helper::printError('\' + message + \''); ?>
                        }
                    }
                );
			}
    	});
	}
	
    function editUser(userId) { 
    	var link = 'Pages/ControlPanel/Modals/user.php?id=' + userId;
    	showModal(link, '<?php echo Session::t('Edit User'); ?>');
    }

    function loginToAccount(id, userId) {
        $.post(
            'Ajax/accountswitch.php',
            {
                id: id,
                userId: userId,
                <?php Csrf::printParameters('SwitchAccount'); ?>
            },
            function(response) {
                response = JSON.parse(response);
                var message = response.message;
                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    location.reload();
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    }

</script>
