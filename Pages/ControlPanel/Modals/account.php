<?php

include_once '../../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$id = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : (int)$_GET['id'];

$accounts = new Accounts($id);

$groups = new Groups();
$groupList = $groups->getAll();

$users = new Users();
$usersList = $users->getAll();

?>

<form id="formAccount" onsubmit="event.preventDefault();">
	<input type="hidden" name="id" value="<?php echo $id; ?>"/>
    <?php Csrf::printFormInputs("Account"); ?>

	<div class="row">
        <div class="col-sm-6 col-xs-12">
        	<div class="form-group">
        		<label class="control-label" for="company"><?php echo Session::t('Name'); ?></label>
				<input id="name" class="form-control" type="text" name="name" autofocus="autofocus" onfocus="cleanError()" value="<?php echo $accounts->getName(); ?>"/>
        	</div>
       	</div>
        <div class="col-sm-3 col-xs-12">
        	<div class="form-group">
        		<label class="control-label" for="groupid"><?php echo Session::t('Group Name'); ?></label>
				<select id="groupid" class="form-control" name="groupid" onchange="cleanError()">
				
				<?php foreach ($groupList AS $group) {
					$selected = ($group['id'] == $accounts->getGroupId()) ? 'selected' : '';
					echo '<option id="' . $group['id'] . '"' . $selected . '  value="' . $group['id'] . '">' . $group['name'] . '</option>';
				} ?>
				
				</select>
        	</div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="active"><?php echo Session::t('Active'); ?></label>
                <?php Helper::printenableDisableOptions("active", $accounts->getActive(), "active"); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php if ($id) { ?>
        	<div class="form-group">
        		<label class="control-label" for="adminid"><?php echo Session::t('Admin Id'); ?></label>
				<select id="adminid" class="form-control" name="adminid" onchange="cleanError()">
				
				<?php foreach ($usersList AS $user) {
					$selected = ($accounts->getAdminId() == $user['id']) ? 'selected' : '';
					if ($user['accountid'] == Session::getAccountId()) {
						echo '<option id="' . $user['id'] . '"' . $selected . '  value="' . $user['id'] . '">' . $user['name'] . ' [' . $user['email'] . ']</option>';
					}
				} ?>
				
				</select>			
        	</div>
            <?php } else { ?>
                <div class="form-group">
                    <label class="control-label"><?php echo Session::t('Admin Email'); ?></label>
                    <input class="form-control" type="text" id="adminEmail" name="adminEmail" value=""/>
                </div>
            <?php } ?>
        </div>
        <div class="col-sm-6 col-xs-12">
            <?php if ($id) { ?>
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Created'); ?></label>
                <input disabled="disabled" class="form-control" type="text"  value="<?php echo $accounts->getCreated(); ?>"/>
            </div>
            <?php } else { ?>
                <div class="form-group">
                    <label class="control-label"><?php echo Session::t('User Name'); ?></label>
                    <input class="form-control" type="text" id="userName" maxlength="25" name="userName" value=""/>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
        	<span id="accountError" class="formError error">&nbsp;</span>
        </div>
    </div>
    <div class="row">
		<div class="col-xs-12 form-footer">
      		<div class="btn-group">
      			<div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                <div id="buttonSaveAccount" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
          	</div>
        </div>
   	</div> 
</form>

<script type="text/javascript">

	$(document).ready(function() {
		$("#groupid, #adminid").select2();

        $("#active").bootstrapToggle({
            on: "<?php echo Session::t('Yes'); ?>",
            off: "<?php echo Session::t('No'); ?>",
            onstyle: 'success',
            offstyle: 'danger'
        });

        $('#buttonSaveAccount').click(function() {
            var name = $('#name').val().trim();

            if (name == '' || name == undefined) {
                $('#accountError').html('<?php echo Session::t('Enter name.'); ?>');
            } else {
                $('#buttonSaveAccount').attr('disabled', 'disabled').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin');

                $.post(
                    'Ajax/account.php',
                    $('#formAccount').serialize(),
                    function(response) {
                        response = JSON.parse(response);
                        message = response.message;
                        $('.btn').removeAttr('disabled');
                        $('#buttonSaveAccount').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            $(".modal .close").click();

                            if (window.tableAllAccounts != undefined) {
                                tableAllAccounts.ajax.reload();
                            }

                            <?php Helper::printSuccess('\' + message + \''); ?>
                        } else {
                            $('#accountError').html(message);
                        }
                    }
                );
            }
        });
	});

</script>
