<?php

include_once '../../functions.php';

Session::authenticateUser();

$header = [
    'title' => Session::t('My Consumer'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-handshake-o',
            'link' => 'Pages/Suppliers/consumers.php',
            'pagename' => Session::t('My Consumer')
        ]
    ]
];

Helper::displayPageHeader($header);

$relations = new Relations();
$relations->setSupplierAccountId(Session::getAccountId());
$supplierConsumers = $relations->getSupplierConsumers();

$supplierConsumer = empty($supplierConsumers) ? null : reset($supplierConsumers);
$id = empty($supplierConsumer['id']) ? null : $supplierConsumer['id'];

$relations = new Relations($id);

$accounts = new Accounts($relations->getConsumerAccountId());
$consumerList = $accounts->getAllConsumers();
$consumerList = !empty($consumerList) && is_array($consumerList) ? $consumerList : [];
$consumerName = !empty($consumerList[$relations->getConsumerAccountId()]['name']) ? $consumerList[$relations->getConsumerAccountId()]['name'] : '';
$consumerAdminEmail = !empty($consumerList[$relations->getConsumerAccountId()]['email']) ? $consumerList[$relations->getConsumerAccountId()]['email'] : '';

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('Consumer Details'); ?></h3>
            </div>
            <div class="box-body">
                <form id="formConsumer" onsubmit="event.preventDefault();">
                    <?php Csrf::printFormInputs("Consumer"); ?>
                    <?php if ($id) { ?>
                        <input id="id" class="form-control tab0" type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <?php } ?>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Session::t('Account'); ?></label>
                                <select id="consumerId" class="form-control" <?php echo $id ? 'disabled="disabled"' : ''; ?> name="consumerId" onchange="updateConsumerDetails();">
                                    <option id="0" value="0" selected="selected" >New Account</option>
                                    <?php foreach ($consumerList as $key => $details) {
                                        if ($id || empty($supplierConsumers[$key])) {
                                            $selected = $key == $relations->getConsumerAccountId() ? 'selected="selected"' : '';

                                            echo '<option id="' . $key . '" value="' . $key . '" ' . $selected . ' data-name="' . $details['name'] . '" data-email="' . $details['email'] . '">' . $details['name'] . '</option>';
                                        }
                                    } ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Session::t('Access Token'); Helper::printPopoverButton(Session::t('Access Token'), Session::t('Leave blank if not applicable.')); ?></label>
                                <div class="input-group">
                                    <input id="apiToken" class="form-control" type="password" name="apiToken"
                                           value="<?php echo Encryption::decrypt($relations->getApiToken(), $relations->getToken()); ?>" />
                                    <span id="showApiToken" class='input-group-addon fa-pointer' title='<?php echo Session::t('Show/Hide'); ?>'><i class='fa fa-eye-slash'></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Session::t('Access Username'); Helper::printPopoverButton(Session::t('Access Username'), Session::t('Leave blank if not applicable.')); ?></label>
                                <input class="form-control" type="text" id="userName" name="userName" onfocus="cleanError()" value="<?php echo $relations->getUserName(); ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Session::t('Access Password'); Helper::printPopoverButton(Session::t('Access Password'), Session::t('Leave blank if not applicable.')); ?></label>
                                <div class="input-group">
                                    <input id="password" class="form-control" type="password" name="password"
                                           value="<?php echo Encryption::decrypt($relations->getPassword(), $relations->getToken()); ?>" />
                                    <span id="showPassword" class='input-group-addon fa-pointer' title='<?php echo Session::t('Show/Hide'); ?>'><i class='fa fa-eye-slash'></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Session::t('Consumer Name'); ?></label>
                                <input class="form-control" id="consumerName" <?php echo $id ? 'disabled="disabled"' : ''; ?> type="text" name="consumerName" onfocus="cleanError()" value="<?php echo $consumerName; ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label"><?php echo Session::t('Consumer Admin Email'); ?></label>
                                <input class="form-control" id="consumerAdminEmail" <?php echo $id ? 'disabled="disabled"' : ''; ?>  type="text" name="consumerAdminEmail" onfocus="cleanError()" value="<?php echo $consumerAdminEmail; ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <span id="error" class="formError error">&nbsp;</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-footer">
                            <div class="btn-group">
                                <div id="btnDeleteConsumer" class="btn btn-danger"><i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Delete'); ?></div>
                                <div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                                <div id="btnSaveConsumer" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(document).ready(function() {
        $("#consumerId").select2({templateResult: formatConsumer});

        $("#showApiToken").click(function() {
            if ($("#apiToken").attr("type") == "text") {
                $("#apiToken").attr("type", "password");
            } else {
                $("#apiToken").attr("type", "text");
            }
        });

        $("#showPassword").click(function() {
            if ($("#password").attr("type") == "text") {
                $("#password").attr("type", "password");
            } else {
                $("#password").attr("type", "text");
            }
        });
    });

    $("#btnDeleteConsumer").click(function () {
        var id = <?php echo empty($id) ? "null" : $id; ?>;

        if (id) {
            $(".btn").attr("disabled", "disabled");
            $("#btnDeleteConsumer").find($(".fa")).removeClass("fa-trash-o fa-red").addClass("fa-refresh fa-spin");

            $.post(
                "Ajax/consumerdelete.php",
                {
                    id: id,
                    <?php Csrf::printParameters("DeleteConsumer"); ?>
                },
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;

                    if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                        <?php Helper::printSuccess('\' + message + \''); ?>
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                    }

                    goToPage('Pages/Suppliers/consumers.php');
                    $('.btn').removeAttr('disabled');
                    $("#btnDeleteConsumer").find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-trash-o fa-red');
                }
            );
        }
    });

    $("#btnSaveConsumer").click(function() {
        $('.btn').prop('disabled', true);

        var consumerId = $('#consumerId').val();
        var apiToken = $('#apiToken').val();
        var userName = $('#userName').val();
        var password = $('#password').val();
        var consumerName = $('#consumerName').val();
        var consumerAdminEmail = $('#consumerAdminEmail').val();

        if (consumerId == 0 && (consumerName == "" || consumerAdminEmail == "")) {
            $('#error').html('<?php echo Session::t('Enter Consumer Name and Admin Email.'); ?>');
            $('.btn').prop('disabled', false);
        } else if (consumerId == 0 && !validateEmail(consumerAdminEmail)) {
            $('#error').html('<?php echo Session::t('Enter Valid Admin Email.'); ?>');
            $('.btn').prop('disabled', false);
        } else if (consumerId == "" || consumerId == undefined) {
            $('#error').html('<?php echo Session::t('Select Consumer.'); ?>');
            $('.btn').prop('disabled', false);
        } else if ((apiToken == "" || apiToken == undefined) && (userName == "" || userName == undefined)) {
            $('#error').html('<?php echo Session::t('Enter Access Api Token or Username and Password.'); ?>');
            $('.btn').prop('disabled', false);
        } else {
            if (consumerId == 0) {
                $.post(
                    'Ajax/account.php',
                    {
                        userName: consumerName,
                        active: 1,
                        adminEmail: consumerAdminEmail,
                        name: consumerName,
                        groupid: <?php echo Groups::GROUP_CONSUMER; ?>,
                        <?php Csrf::printParameters('Consummer'); ?>
                    },
                    function(response) {
                        response = JSON.parse(response);
                        message = response.message;

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            $('#consumerId').val(consumerId);
                            saveConsumer(response.data);
                        } else {
                            <?php Helper::printError('\' + message + \''); ?>
                        }
                    }
                );
            } else {
                saveConsumer(null);
            }
        }

        return false;
    });

    function updateConsumerDetails() {
        cleanError();

        var consumerName = $('#consumerId option:selected').attr('data-name');
        var consumerAdminEmail = $('#consumerId option:selected').attr('data-email');
        var consumerId = $('#consumerId').val();

        if (consumerName != undefined && consumerId != 0) {
            $('#consumerName').val(consumerName).prop('disabled', true);
            $('#consumerAdminEmail').val(consumerAdminEmail).prop('disabled', true);
        } else {
            $('#consumerName, #consumerAdminEmail').val('').prop('disabled', false);
        }
    }

    function saveConsumer(consumerId) {
        var data = $('#formConsumer').serialize();

        if (consumerId) {
            data += '&consumerId=' + consumerId;
        }

        $('#btnSaveConsumer').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin');

        $.post(
            "Ajax/relation.php",
            data,
            function(response) {
                var response = JSON.parse(response);
                var message = response.message;
                $('.btn').removeAttr('disabled');

                if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }

                $('.btn').prop('disabled', true);
                $('#btnSaveConsumer').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');
                goToPage('Pages/Suppliers/consumers.php');
            }
        );
    }

</script>
