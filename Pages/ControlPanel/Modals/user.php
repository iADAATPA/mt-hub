<?php

include_once '../../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$id = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : (int)$_GET['id'];
$users = new Users($id);
$accounts = new Accounts();
$accountList = $accounts->getAll();

?>

<form id="formUser" onsubmit="event.preventDefault();">
    <?php Csrf::printFormInputs("UserAdmin"); ?>
    <!-- Hidden input to pass to the ajax file -->
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>

	<div class="row">
        <div class="col-sm-6 col-xs-12">
        	<div class="form-group">
        		<label class="control-label" for="name"><?php echo Session::t('User Name'); ?></label>
                <input id="name" class="form-control" type="text" name="name" onfocus="cleanError()" value="<?php echo $users->getName(); ?>"/>
        	</div>
       	</div>
        <div class="col-sm-6 col-xs-12">
        	<div class="form-group">
				<label class="control-label" for="email"><?php echo Session::t('Email'); ?></label>
				<input id="email" class="form-control" type="text" name="email" value="<?php echo $users->getEmail(); ?>"/>
        	</div>
        </div>
    </div>
    <?php if ($id) { ?>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
        	<div class="form-group">
        		<label class="control-label" for="created"><?php echo Session::t('Created'); ?></label>
                <input class="form-control" type="text" disabled="disabled" value="<?php echo $users->getCreated(); ?>"/>
        	</div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="created"><?php echo Session::t('Last Login'); ?></label>
                <input class="form-control" type="text" disabled="disabled" value="<?php echo $users->getLastlogin(); ?>"/>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="adminsEmail"><?php echo Session::t('Account'); ?></label>
                <select id="accountId" class="form-control" name="accountId" onchange="cleanError()">

                    <?php foreach ($accountList AS $account) {
                        if ($account['active']) {
                            $selected = ($users->getAccountId() == $account['id']) ? 'selected' : '';
                            echo '<option id="' . $account['id'] . '"' . $selected . '  value="' . $account['id'] . '"> [' . $account['id'] . '] ' . $account['name'] . '</option>';
                        }
                    }; ?>

                </select>
            </div>
        </div>
        <?php if ($id) { ?>
        <div class="col-sm-3 col-xs-6">
            <div class="form-group">
                <label class="control-label" for="loginAttempts"><?php echo Session::t('Failed Logins'); ?></label>
                <input id="loginAttempts" class="form-control" type="number" min="0" max="6" name="loginAttempts" onfocus="cleanError()" value="<?php echo $users->getLoginAttempts(); ?>"/>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-xs-12">
        	<span id="userError" class="formError error">&nbsp;</span>
        </div>
    </div>
    <div class="row">
		<div class="col-xs-12 form-footer">
      		<div class="btn-group">
                <?php if ($id) { ?>
      			<div id="btnDeleteUser" class="btn btn-danger"><i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Delete User'); ?></div>
    			<?php } ?>
                <div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                <div id="btnSaveUser" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
          	</div>
        </div>
   	</div>
</form>

<script type="text/javascript">

    $(document).ready(function() {
        $("#accountId").select2();
    });

    $("#btnSaveUser").click(function() {
        $('#btnSaveUser').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin').attr('disabled', 'disabled');

        $.post(
            "Ajax/useradmin.php",
            $("#formUser").serialize(),
            function(response) {
                response = JSON.parse(response);
                message = response.message;
                $('.btn').removeAttr('disabled');
                $('#btnSaveUser').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');

                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    $(".modal .close").click();

                    if (window.tableAllUsers != undefined) {
                        tableAllUsers.ajax.reload();
                    }

                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    $('#userError').html(message);
                }
            }
        );
    });

    $('#btnDeleteUser').click(function () {
        dialogConfirm("<?php echo Session::t('Are you sure you want to delete user?'); ?>", "<?php echo Session::t('Delete User'); ?>",  function(response) {
        	if (response == 1) {
                $('#btnDeleteUser').find($('.fa')).removeClass('fa-trash-o').addClass('fa-refresh fa-spin').attr('disabled', 'disabled');

                $.post(
                    "Ajax/userdelete.php",
                    $("#formUser").serialize(),
                    function(response) {
                        response = JSON.parse(response);
                        message = response.message;
                        $('#btnDeleteUser').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-trash-o').removeAttr('disabled');

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            $(".modal .close").click();

                            if (window.tableAllUsers != undefined) {
                                tableAllUsers.ajax.reload();
                            }

                            <?php Helper::printSuccess('\' + message + \''); ?>
                        } else {
                            $('#userError').html(message);
                        }
                    }
                );
			}
        });
    });

</script>
