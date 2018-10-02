<?php

include_once '../../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$engineList = empty($_GET['engines']) ? null : trim($_GET['engines']);
$accounts = new Accounts();
$accountList = $accounts->getAll();

?>

<form id="copyEngine" onsubmit="event.preventDefault();">
    <input type="hidden" id="engines" value="<?php echo $engineList; ?>" />
	<div class="row">
        <div class="col-sm-6 col-xs-12">
        	<div class="form-group">
        		<label class="control-label"><?php echo Session::t('Copy to Account'); ?></label>
		        <select id="account" class="form-control" name="account" onfocus="cleanError()">
		        
				<?php if ($accountList) {
					foreach ($accountList as $account) {
						if (in_array($account['groupid'], [Groups::GROUP_ADMINISTRATOR, Groups::GROUP_SUPPLIER])) {
                            echo '<option id="' . $account['id'] . '" value="' . $account['id'] . '">';
                            echo '[' . $account['id'] . '] ' . $account['name'] . '</option>';
                        }
					}
				} ?>
				
		        </select>       	
        	</div>
       	</div>
    </div>
	<div class="row">
        <div class="col-xs-12">
        	<span id="copyEngineError" class="formError error">&nbsp;</span>        	
        </div>
    </div>
    <div class="row">
		<div class="col-xs-12 form-footer">
      		<div class="btn-group">
    			<div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                <div id="buttonAdminCopyEngine" class="btn btn-info"><i class="fa fa-files-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Copy'); ?></div>
          	</div>
        </div>
   	</div>
</form>

<script type="text/javascript">

    $(document).ready(function() {    
		$("#account").select2();
    });

    $("#buttonAdminCopyEngine").click(function() {
		var account = $('#account').val();
		var engines = $('#engines').val();

    	if (account != "" && account != undefined && engines != '' && engines != undefined) {
            $('#btnCopyEngine').attr('disabled', 'disabled').find($('.fa')).removeClass('fa-files-o').addClass('fa-refresh fa-spin');
            var engineList = engines.split(',');
            var i = 0;
            engineList.forEach(function(id) {
                i++;
                $.post(
                    "Ajax/enginecopy.php",
                    {
                        id: id,
                        accountId: account,
                        <?php Csrf::printParameters("CopyEngine"); ?>
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

                if (i == engineList.length) {
                    tableAllEngines.ajax.reload(null, false);
                    $("#buttonAdminCopyEngine").removeAttr('disabled').find($(".fa")).removeClass('fa-refresh fa-spin').addClass('fa-files-o');
                    $(".modal .close").click();
                }
            });
		} else {
            $(".modal .close").click();
            <?php Helper::printError(Session::t('An unknown error occurred!')); ?>
    	}		
    });

</script>
