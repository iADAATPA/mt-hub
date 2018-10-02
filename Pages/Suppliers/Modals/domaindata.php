<?php

include_once '../../../functions.php';

Session::authenticateUser();

$domainId = empty($_GET['id']) || !is_numeric($_GET['id']) ? Session::getActiveDomainId() : $_GET['id'];

?>

<form id="formaDomainData" onsubmit="event.preventDefault();">
    <?php Csrf::printFormInputs("DomainData"); ?>
    <input id="id" class="form-control tab0" type="hidden" name="id" value="<?php echo $domainId; ?>"/>
    <div class="row padding-bottom-10">
        <div class="col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="author"><?php echo Session::t('Segments'); ?></label>
                <textarea class="form-control noResize" name="segments" rows="10"></textarea>
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
                <div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                <div id="btnSaveMetaData" class="btn btn-info"><span id="saveMetaDataIcon"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></span>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function() {
        $("#btnSaveMetaData").click(function () {
            // Disable the finish button
            $('#btnSaveMetaData').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin').attr('disabled', 'disabled');

            $.post(
                "Ajax/domaindata.php",
                $("#formaDomainData").serialize(),
                function (response) {
                    response = JSON.parse(response);
                    var message = response.message;
                    if (window.tableAllDomains != undefined) {
                        tableAllDomains.ajax.reload();
                    }

                    tableAllDomains.ajax.reload();
                    if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                        $(".modal .close").click();

                        <?php Helper::printSuccess('\' + message + \''); ?>
                    } else {
                        $(".modal .close").click();
                        <?php Helper::printError('\' + message + \''); ?>
                    }
                }
            );

            return false;
        });
    });

</script>
