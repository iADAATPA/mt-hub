<?php

include_once '../../../functions.php';

Session::authenticateUser();

$id = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : (int)$_GET['id'];
$relations = new Relations($id);

$relations->setConsumerAccountId(Session::getAccountId());
$consumerSuppliers = $relations->getConsumerSuppliers();

$accounts = new Accounts($relations->getConsumerAccountId());
$supplierList = $accounts->getAllSuppliers();

$suppiersAssignedToConsumers = empty($id) ? $relations->getSuppliersAssignedToConsumer() : [];
$supplierList = !empty($supplierList) && is_array($supplierList) ? $supplierList : [];
$name = !empty($supplierList[$relations->getSupplierAccountId()]['name']) ? $supplierList[$relations->getSupplierAccountId()]['name'] : '';
$adminEmail = !empty($supplierList[$relations->getSupplierAccountId()]['email']) ? $supplierList[$relations->getSupplierAccountId()]['email'] : '';

?>

<form id="formSupplier" onsubmit="event.preventDefault();">
    <?php Csrf::printFormInputs("Consumer"); ?>
    <?php if ($id) { ?>
        <input id="id" class="form-control tab0" type="hidden" name="id" value="<?php echo $id; ?>"/>
        <input class="form-control" type="hidden" name="supplierTokenCheck"  value="<?php echo Helper::maskToken(Encryption::decrypt($relations->getApiToken(), $relations->getToken())); ?>"/>
    <?php } ?>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Supplier'); ?></label>
                <select id="supplierId" class="form-control" <?php echo $id ? 'disabled="disabled"' : ''; ?> name="supplierId">
                    <?php foreach ($supplierList as $key => $details) {
                        if (($id || empty($consumerSuppliers[$key]) && (empty($id) && !in_array($key, $suppiersAssignedToConsumers)))) {
                            $selected = $key == $relations->getSupplierAccountId() ? 'selected="selected"' : '';

                            echo '<option id="' . $key . '" value="' . $key . '" ' . $selected . ' data-name="' . $details['name'] . '" data-email="' . $details['email'] . '">' . $details['name'] . '</option>';
                        }
                    } ?>

                </select>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Access Token'); Helper::printPopoverButton(Session::t('Access Token'), Session::t('Leave blank if not applicable.')); ?></label>
                <input class="form-control" id="token" type="text" name="token" onfocus="cleanError()" value="<?php echo Helper::maskToken(Encryption::decrypt($relations->getApiToken(), $relations->getToken())); ?>"/>
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
                <input class="form-control" type="password" id="password" name="password" onfocus="cleanError()" value="<?php echo Helper::maskToken(Encryption::decrypt($relations->getPassword(), $relations->getToken())); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <span class="error" id="error">&nbsp;</span>
        <div class="col-xs-12 form-footer">
            <div class="btn-group">
                <div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                <div id="btnSaveSupplier" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function() {
        $("#supplierId").select2();

        $('#supplierToken').on('click focusin', function() {
            this.value = '';
        });

        $("#btnSaveSupplier").click(function() {
            $('.btn').prop('disabled', true);
            $('#btnSaveSupplier').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin');
            var supplierId = $('#supplierId').val();
            var token = $('#token').val();
            var userName = $('#userName').val();
            var password = $('#password').val();

            if (supplierId == "" || supplierId == undefined) {
                $('#error').html('<?php echo Session::t('Select Supplier.'); ?>');
                $('.btn').prop('disabled', false);
                $('#btnSaveSupplier').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');
            } else if ((token == "" || token == undefined) && (userName == "" || userName == undefined)) {
                $('#error').html('<?php echo Session::t('Enter Access Api Token or Username and Password.'); ?>');
                $('.btn').prop('disabled', false);
                $('#btnSaveSupplier').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');
            } else {
                var data = $('#formSupplier').serialize();

                $.post(
                    "Ajax/relation.php",
                    data,
                    function(response) {
                        response = JSON.parse(response);
                        message = response.message;
                        $('.btn').removeAttr('disabled');
                        $('#btnSaveSupplier').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin');

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            $(".modal .close").click();

                            if (window.tableSuppliers != undefined) {
                                tableSuppliers.ajax.reload();
                            }

                            <?php Helper::printSuccess('\' + message + \''); ?>
                        } else {
                            $(".modal .close").click();
                            <?php Helper::printError('\' + message + \''); ?>
                        }

                        $('#btnSaveSupplier').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');
                    }
                );
            }

            return false;
        });
    });

</script>
