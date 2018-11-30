<?php

if (!empty($_GET['token'])) {
    $token = Helper::sanitizeString($_GET['token']);
    $loginDisplay = 'style="display:none;"';
    ?>

    <script type="text/javascript">

        $(document).ready(function() {
            $('#login').hide();
            $('#setPassword').show();
            $('input[type=password]').val('')
        });

    </script>

    <?php

} else {
    $loginDisplay = '';
    $token = '';
}

?>

<div class="container">
    <div class="row row-xs-offset-1">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" id="login" <?php echo $loginDisplay; ?>>
                <div class="panel-heading panel-primary"><?php echo Session::t('Login'); ?></div>
                <div class="panel-body">
                    <form id="formLogin" class="form-horizontal" role="form">
                        <div id="emailGroup" class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <label class="control-label"><?php echo Session::t('User Name'); ?></label>
                                <input id="name" name="name" type="text" class="form-control" value="" required autofocus />
                                <label id="userName-error" class="error" for="name"></label>
                            </div>
                        </div>
                        <div id="passwordGroup" class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <label class="control-label"><?php echo Session::t('Password'); ?></label>
                                <input id="password" name="password" type="password" class="form-control" required />
                                <label id="password-error" class="error" for="password"></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2 text-right">
                                <div id="btnLogin" class="btn btn-info"><i class="fa fa-sign-in fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Login'); ?></div>
                            </div>
                            <div class="col-md-8 col-md-offset-2 padding-top-10">
                                <a class="btn btn-link no-padding" id="forgotLink"><?php echo Session::t('Forgot Your Password?'); ?></a>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <a class="btn btn-link no-padding font-small" id="privacyPolicy"><?php echo Session::t('Our Privacy Policy'); ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel panel-default" id="forgot" style="display:none;">
                <div class="panel-heading panel-primary"><?php echo Session::t('Forgot Password'); ?></div>
                <div class="panel-body">
                    <form id="formForgotPassword" class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <label class="control-label"><?php echo Session::t('User Name'); ?></label>
                                <input id="name" name="name" type="text" class="form-control" value="" required />
                                <label id="userName-error" class="error" for="name"></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2 text-right">
                                <div id="btnReset" class="btn btn-info"><i class="fa fa-unlock fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Reset'); ?></div>
                            </div>
                            <div class="col-md-8 col-md-offset-2 padding-top-10">
                                <a class="btn btn-link no-padding" id="loginLink"><?php echo Session::t('Return to Login'); ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel panel-default" id="setPassword" style="display:none;">
                <div class="panel-heading panel-primary"><?php echo Session::t('Set Password'); ?></div>
                <div class="panel-body">
                    <form id="formSetPassword" class="form-horizontal" role="form">
                        <!-- hidden input to pass to the ajax file instance id -->
                        <input id='token' type='hidden' name='token' value='<?php echo $token; ?>'/>

                        <div id="newPasswordGroup" class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <label class="control-label"><?php echo Session::t('New Password:'); ?></label>
                                <input id="newPassword" name="newPassword" type="password" class="form-control" value="" required />
                                <label id="newPassword-error" class="error" for="newPassword"></label>
                            </div>
                        </div>
                        <div id="reenteredPasswordGroup" class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <label class="control-label"><?php echo Session::t('Confirm New Password'); ?></label>
                                <input id="reenteredPassword" name="reenteredPassword" type="password" class="form-control" value="" required />
                                <label id="reenteredPassword-error" class="error" for="reenteredPassword"></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2 text-right">
                                <div id="btnSetPassword" class="btn btn-info"><i class="fa fa-save fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Set Password'); ?></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $redirectURL = 'index.php'; ?>

<script type="text/javascript">

    $(document).ready(function() {
        $('#forgotLink').click(function(){
            $('#login').hide();
            $('#forgot').show();
        });

        $('#loginLink').click(function(){
            $('#forgot').hide();
            $('#login').show();
        });

        $("#btnLogin").click(function() {
            $('#btnLogin').find($('.fa')).removeClass('fa-sign-in').addClass('fa-refresh fa-spin');
            $.post('Ajax/userlogin.php', $('#formLogin').serialize(), function(response) {
                response = JSON.parse(response);
                var message = response.message;
                $('#btnLogin').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-sign-in');

                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    window.open('index.php', '_self');
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            });
        });

        $("#btnReset").click(function() {
            $('#btnReset').find($('.fa')).removeClass('fa-unlock').addClass('fa-refresh fa-spin');

            $.post('Ajax/userforgotpassword.php', $('#formForgotPassword').serialize(), function(response) {
                response = JSON.parse(response);
                var message = response.message;
                $('#btnReset').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-unlock');

                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    $('#forgot').hide();
                    $('#login').show();
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            });
        });

        $("#btnSetPassword").click(function() {
            $('#btnSetPassword').find($('.fa')).removeClass('fa-save').addClass('fa-refresh fa-spin');

            $.post('Ajax/usersetpassword.php', $('#formSetPassword').serialize(), function(response) {
                response = JSON.parse(response);
                var message = response.message;
                $('#btnSetPassword').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-save');

                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    $('#setPassword').hide();
                    $('#login').show();
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            });
        });

        <?php if(!Helper::getEuDirectiveCookie()) { ?>

            var errorId = 'euDirectivePolicy';

            $('#alertBox').prepend('<div class="alert alert-info" id="' + errorId + '" style="display: none;">' +
                '<button type="button" class="close" aria-hidden="true">&times;</button>' +
                '<?php echo Session::t("This site uses cookies. Some of the cookies we use are essential in order for parts of the site to operate and have already been set. You may delete all cookies, but some parts of the site might not work."); ?>' +
                '</div>'
            );

            $('#' + errorId).fadeIn('slow');

            $('#' + errorId).on('click', 'button.close', function() {
                $('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() {
                    $('#' + errorId).remove();
                    createCookie('MT-HUBEuDirective', 1);
                });
            });

        <?php } ?>
    });

</script>

<?php Helper::printModal('privacyPolicy', 'Help/privacypolicy.php', Session::t('Privacy Statement')); ?>
