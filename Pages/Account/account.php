<?php

include_once '../../functions.php';

Session::authenticateUser();

$accountId = Session::getAccountId();
$account = new Accounts($accountId);

$apiToken = $account->getApiToken();
$logo = $account->getLogo();
$date = new DateTime($account->getCreated());
$created = $date->format('M jS, Y');

$dragAndDrop = new DragAndDrop();
$dragAndDrop->setDatabaseTable('accounts');
$dragAndDrop->makeDragAndDropZone('logoPicture');

?>

<div class="box-body">
    <div class="box-header">
        <h3 class="box-title"><?php echo Session::t('Account'); ?></h3>
    </div>
    <form id="accountDetails" enctype="multipart/form-data">
        <div class="row">
        	<div class="col-md-4">
    			<div class="col-md-12 company-logo">
                  	<label for="name"><?php echo Session::t('Company Logo'); ?></label>
                	<div class="form-group padding-top-20 text-center" id="logoPicture" title="<?php echo Session::t('Company Logo'); ?>">
                     	<img class="logo-img img-responsive" style="background-color: transparent;" src="<?php echo $logo; ?>" onError="this.onerror=null;this.src='Images/logo.png';" alt="<?php echo Session::t('Company Logo'); ?>">
                   		<span class="deletePicture" title="<?php echo Session::t('Delete Company Logo'); ?>">
                    		&nbsp;
                    	</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Created'); ?></label>
                        <input class="form-control" type="text" disabled="disabled" value="<?php echo $created; ?>" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Consumer Token Id'); ?></label>
                        <div class="input-group">
                            <input id="apiToken" class="form-control" type="text" name="apiToken" value="<?php echo $apiToken; ?>" readonly />
                            <span class='input-group-addon fa-pointer' title='<?php echo Session::t('Copy'); ?>' onclick="copyApiToken()"><i class='fa fa-copy'></i></span>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    	<div class="row">
    		<div class="col-md-12">
        		<div class="btn-group pull-right">
        			<div id="deleteAccount" class="btn btn-danger"><i class="fa fa-trash fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Delete Account'); ?></div>
                    <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
                    <div id="btnRegenerateApiToken" class="btn btn-default"><i class="fa fa-recycle fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Regenerate Token'); ?></div>
                    <?php } ?>
                </div>
        		<button class="btn btn-info btn-sm hidden" id="submitPhoto" value="submitPhoto" name="button" type="submit">&nbsp;</button>
        	</div>
        </div>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function() {
		var currentUserImg = $('.logo-img').attr('src');

		if (currentUserImg == 'Images/logo.png') {
			$('.deletePicture').hide();
		}
	});

	$('.deletePicture').click(function(){
		var currentUserImg = $('.logo-img').attr('src');

		if (currentUserImg != "Images/logo.png") {
			$.post(
			    "Ajax/imagedelete.php",
                {
                    table: "accounts",
                    <?php Csrf::printParameters("ImageDelete"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    var message = response.message;

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        $('.logo-img').attr('src', 'Images/logo.png');
                        $('.deletePicture').hide();
                        <?php Helper::printSuccess('\' + message + \''); ?>
                    }
                }
			);
		}
	});

    <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
	$('#btnRegenerateApiToken').click(function() {
        dialogConfirm("<?php echo Session::t("Are you sure you want to regenerate your API token? This cannot be undone!"); ?>", "<?php echo Session::t("Regenerate Token"); ?>", function (response) {
            if (response == true) {
                $('#btnRegenerateApiToken').find($('.fa')).removeClass('fa-recycle').addClass('fa-refresh fa-spin');

                $.post(
                    "Ajax/accountregenerateapi.php",
                    {
                        <?php Csrf::printParameters("RegenerateToken"); ?>
                    },
                    function(response) {
                        response = JSON.parse(response);
                        var message = response.message;
                        var token = response.data;
                        $("#btnRegenerateApiToken").find($(".fa")).removeClass("fa-refresh fa-spin").addClass("fa-recycle");

                        if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                            window.apiToken = token;
                            $("#apiToken").val(token);
                            <?php Helper::printSuccess('\' + message + \''); ?>
                        } else {
                            <?php Helper::printError('\' + message + \''); ?>
                        }
                    }
                );
            }
        });
	});
	<?php } ?>

    $('#deleteAccount').click(function() {
        dialogConfirmPassword(
            "<?php echo Session::t("Please enter your password to confirm that you would like to permanently delete your account."); ?>",
            "",
            "<?php echo Session::t("Delete Account"); ?>",
            confirmDeletion
        );
    });

    function confirmDeletion(accountPassword) {
         if (accountPassword != null) {
            if (accountPassword == '') {
                <?php Helper::printError(Session::t("Please enter your password.")); ?>
            }
            else {
                $.post(
                    "Ajax/accountdelete.php",
                    {
                        password: accountPassword,
                        check: 1,
                        <?php Csrf::printParameters("AccountDelete"); ?>
                    },
                    function(response) {
                        response = JSON.parse(response);
                        var message = response.message;

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            dialogConfirm(
                                "<?php echo Session::t("Are you sure you want to delete your account?"); ?>",
                                "<?php echo Session::t("Delete Account"); ?>",
                                deleteAccount
                            );
                        } else {
                            <?php Helper::printError('\' + message + \''); ?>
                        }
                    }
                );
            }
        }
    }

    function deleteAccount(result){
        if (result == 1) {
            $.post(
                "Ajax/accountdelete.php",
                {
                    id: <?php echo Session::getAccountId(); ?>,
                    <?php Csrf::printParameters("AccountDelete"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    var message = response.message;

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        $.post(
                            "Ajax/destroysession.php",
                            function(response) {
                                window.location = "../../index.php";
                            }
                        );
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                    }
                }
            );
        }
    }

    <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
    function copyApiToken() {
        copyToClipboard("apiToken");
        <?php Helper::printSuccess(Session::t("API token copied to clipboard")); ?>
    }
    <?php } ?>

</script>
