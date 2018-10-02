<?php

include_once '../../functions.php';

Session::authenticateUser();

$accountId = Session::getAccountId();
$userId = Session::getUserId();
$account = new Accounts($accountId);
$users = new Users($userId);

$userEmail = $users->getEmail();
$photo = $users->getPhoto() ? $users->getPhoto() : 'Images/user.png';
$name = $users->getName() ? $users->getName() : '';
$date = new DateTime($users->getCreated());
$created = $date->format('M jS, Y');

$dragAndDrop = new DragAndDrop();
$dragAndDrop->setDatabaseTable('users');
$dragAndDrop->makeDragAndDropZone('userPicture');

?>

<div class="box-body">
	<div class="box-header">
		<h3 class="box-title"><?php echo Session::t('User'); ?></h3>
	</div>
    <form id="formUser" enctype="multipart/form-data">
        <?php Csrf::printFormInputs("User"); ?>
        <div class="row">
        	<div class="col-md-4">
    			<div class="col-md-12">
                  	<label for="name">User Picture:<?php Helper::printPopoverButton(Session::t('User Picture'), Session::t('Simply drag and drop a picture to update this area.')); ?></label>
                	<div class="form-group padding-top-20" id="userPicture" title="<?php echo Session::t('User Picture'); ?>">
                     	<img class="profile-user-img img-responsive img-circle" src="<?php echo $photo; ?>" onError="this.onerror=null;this.src='/Images/user.png';" alt="<?php echo Session::t('User Picture'); ?>">
                    	<span class="deletePicture" title="<?php echo Session::t('Delete User Picture'); ?>">
                    		&nbsp;
                    	</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Email'); ?></label>
                        <input id="useremail" class="form-control" type="text" name="email" value="<?php echo $userEmail; ?>" size="30" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Created'); ?></label>
                        <input class="form-control" type="text" disabled="disabled" value="<?php echo $created; ?>" size="30" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('User Name'); ?></label>
                        <input id="name" class="form-control" type="text" name="name" disabled="disabled" value="<?php echo $name; ?>" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
            	<div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Current Password'); ?></label>
                        <input id="password" class="form-control" type="password" name="password" value="" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('New Password'); ?></label>
                        <input id="newPassword" class="form-control" disabled="disabled" type="password" name="newPassword" value="" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"><?php echo Session::t('Confirm New Password'); ?></label>
                       <input id="reenteredPassword" class="form-control" disabled="disabled" type="password" name="reenteredPassword" value="" />
                    </div>
                </div>
            </div>
        </div>
    	<div class="row">
    		<div class="col-md-12">
        		<div class="btn-group pull-right">
        			<div id="btnUpdateUser" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Update User'); ?></div>
        		</div>
        	</div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function() {
        var currentUserImg = $('.profile-user-img').attr('src');
        setTimeout(function(){ $('#password').val(''); }, 1000);

        if (currentUserImg == 'Images/user.png') {
            $('.deletePicture').hide();
        }
    });

    $('#password').keypress(function() {
        if ($('#password').val().length > 4) {
            $('#newPassword').removeAttr('disabled');
            $('#reenteredPassword').removeAttr('disabled');
        } else {
            $('#newPassword').attr('disabled', 'disabled').val('');
            $('#reenteredPassword').attr('disabled', 'disabled').val('');
        }
    });

    $('.deletePicture').click(function(){
        var currentUserImg = $('.profile-user-img').attr('src');

        if (currentUserImg != 'Images/user.png') {
            $.post(
                "Ajax/imagedelete.php",
                {
                    table: "users",
                    <?php Csrf::printParameters("ImageDelete"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    var message = response.message;

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        $('.profile-user-img').attr('src', 'Images/user.png');
                        $('#headerProfileImage').attr('src', 'Images/user.png');
                        $('.deletePicture').hide();
                        <?php Helper::printSuccess('\' + message + \''); ?>
                    }
                }
            );
        }
    });

    $('#btnUpdateUser').click(function() {
        $('#btnUpdateUser').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin');
        $('.btn').attr('disabled', 'disabled');

        $.post(
            "Ajax/user.php",
            $("#formUser").serialize(),
            function(response) {
                response = JSON.parse(response);
                var message = response.message;
                $('#btnUpdateUser').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o');
                $('#newPassword, #reenteredPassword, #password').val('');
                $('#newPassword, #reenteredPassword').attr('disabled', 'disabled');
                $('.btn').removeAttr('disabled');

                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    });

</script>
