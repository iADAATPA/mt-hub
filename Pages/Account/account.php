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
        <?php Csrf::printFormInputs("accountDetails"); ?>
        <input type="hidden" name="id" value="<?php echo $accountId; ?>"/>
        <div class="row">
            <div class="col-md-4">
                <div class="col-md-12 company-logo">
                    <label for="name"><?php echo Session::t('Company Logo'); ?></label>
                    <div class="form-group padding-top-20 text-center" id="logoPicture"
                         title="<?php echo Session::t('Company Logo'); ?>">
                        <img class="logo-img img-responsive" style="background-color: transparent;"
                             src="<?php echo $logo; ?>" onError="this.onerror=null;this.src='Images/logo.png';"
                             alt="<?php echo Session::t('Company Logo'); ?>">
                        <span class="deletePicture" title="<?php echo Session::t('Delete Company Logo'); ?>">
                    		&nbsp;
                    	</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Created'); ?></label>
                        <input class="form-control" type="text" disabled="disabled" value="<?php echo $created; ?>"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="active"><?php echo Session::t('Active'); ?></label>
                        <?php Helper::printenableDisableOptions("active", $account->getActive(), "active"); ?>
                    </div>
                </div>
                <?php if (in_array(Session::getGroupId(), [Groups::GROUP_SUPPLIER])) { ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('ActiviaTM User Name'); ?></label>
                            <input class="form-control"
                                   id="activiaTmUserName" <?php echo $account->getActiviaTm() ? '' : 'disabled="disabled"'; ?>
                                   name="activiaTmUserName" type="text"
                                   value="<?php echo htmlentities($account->getActiviaTmUserName()); ?>"/>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-4">
                <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Consumer Token Id'); ?></label>
                            <div class="input-group">
                                <input id="apiToken" class="form-control" type="text" name="apiToken"
                                       value="<?php echo $apiToken; ?>" readonly/>
                                <span class='input-group-addon fa-pointer' title='<?php echo Session::t('Copy'); ?>'
                                      onclick="copyApiToken()"><i class='fa fa-copy'></i></span>
                            </div>
                        </div>
                    </div>
                <?php } elseif (in_array(Session::getGroupId(), [Groups::GROUP_SUPPLIER])) { ?>
                    <div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="cache"><?php echo Session::t('Local cache'); ?></label>
                            <?php Helper::printenableDisableOptions("cache", $account->getCache(), "cache"); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="activiaTm"><?php echo Session::t('ActiviaTM'); ?></label>
                            <?php Helper::printenableDisableOptions("activiaTm", $account->getActiviaTm(), "activiaTm"); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('ActiviaTM Password'); ?></label>
                            <input class="form-control"
                                   id="activiaTmPassword" <?php echo $account->getActiviaTm() ? '' : 'disabled="disabled"'; ?>
                                   name="activiaTmPassword" type="password"
                                   value="<?php echo empty($account->getActiviaTmPassword()) ? '' : Encryption::decrypt($account->getActiviaTmPassword(),
                                       $account->getToken()); ?>"/>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <div id="btnDeleteAccount" class="btn btn-danger"><i class="fa fa-trash fa-fw"
                            aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Delete Account'); ?>
                    </div>
                    <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
                        <div id="btnRegenerateApiToken" class="btn btn-default"><i class="fa fa-recycle fa-fw"
                               aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Regenerate Token'); ?>
                        </div>
                    <?php } ?>
                    <div id="btnCancel" class="btn btn-default"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;
                            <?php echo Session::t('Cancel'); ?>
                    </div>
                    <div id="btnSaveAccount" class="btn btn-info"><i class="fa fa-floppy-o fa-fw"
                            aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        var currentUserImg = $('.logo-img').attr('src');

        if (currentUserImg == 'Images/logo.png') {
            $('.deletePicture').hide();
        }

        $("#cache, #activiaTm").bootstrapToggle({
            on: "<?php echo Session::t('Enabled'); ?>",
            off: "<?php echo Session::t('Disabled'); ?>",
            onstyle: 'success',
            offstyle: 'danger'
        });

        $("#active").bootstrapToggle({
            on: "<?php echo Session::t('Yes'); ?>",
            off: "<?php echo Session::t('No'); ?>",
            onstyle: 'success',
            offstyle: 'danger'
        });


        $("#activiaTm").change(function () {
            if ($(this).prop('checked')) {
                $("#activiaTmUserName, #activiaTmPassword").prop("disabled", false);
            } else {
                $("#activiaTmUserName, #activiaTmPassword").val("").prop("disabled", true);
            }
        })

        $('#btnCancel').click(function () {
            $("#account").html(loader).load("Pages/Account/account.php");
        });

        $('.deletePicture').click(function () {
            var currentUserImg = $('.logo-img').attr('src');

            if (currentUserImg != "Images/logo.png") {
                $.post(
                    "Ajax/imagedelete.php",
                    {
                        table: "accounts",
                        <?php Csrf::printParameters("ImageDelete"); ?>
                    },
                    function (response) {
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
        $('#btnRegenerateApiToken').click(function () {
            dialogConfirm(
                "<?php echo Session::t("Are you sure you want to regenerate your API token? This cannot be undone!"); ?>",
                "<?php echo Session::t("Regenerate Token"); ?>", function (response)
            {
                if (response == true) {
                    $('#btnRegenerateApiToken').find($('.fa')).removeClass('fa-recycle').addClass('fa-refresh fa-spin');

                    $.post(
                        "Ajax/accountregenerateapi.php",
                        {
                            <?php Csrf::printParameters("RegenerateToken"); ?>
                        },
                        function (response) {
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

        $('#btnDeleteAccount').click(function () {
            dialogConfirmPassword(
                "<?php echo Session::t("Please enter your password to confirm that you would like to permanently delete your account."); ?>",
                "",
                "<?php echo Session::t("Delete Account"); ?>",
                confirmDeletion
            );
        });

        $('#btnSaveAccount').click(function () {
            $("#btnSaveAccount").find($(".fa")).removeClass("fa-floppy-o").addClass("fa-refresh fa-spin");
            $('.btn').attr('disabled', 'disabled');

            $.post(
                "Ajax/account.php",
                $("#accountDetails").serialize(),
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;

                    $("#btnSaveAccount").find($(".fa")).removeClass("fa-refresh fa-spin").addClass("fa-floppy-o");
                    $('.btn').removeAttr('disabled');

                    if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                        <?php Helper::printSuccess('\' + message + \''); ?>
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                    }
                }
            );
        });
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
                    function (response) {
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

    function deleteAccount(result) {
        if (result == 1) {
            $.post(
                "Ajax/accountdelete.php",
                {
                    id: <?php echo Session::getAccountId(); ?>,
                    <?php Csrf::printParameters("AccountDelete"); ?>
                },
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        $.post(
                            "Ajax/destroysession.php",
                            function (response) {
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
