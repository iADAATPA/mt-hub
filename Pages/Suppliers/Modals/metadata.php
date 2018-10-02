<?php

include_once '../../../functions.php';

Session::authenticateUser();

$engineId = empty($_GET['id']) || !is_numeric($_GET['id']) ? Session::getActiveEngineId() : $_GET['id'];
$edit = empty($_GET['edit']) ? false : true;
$metaData = new MetaData();
$metaData->setEngineId($engineId);
$existingMetaData = $metaData->getAll();
$metaDataList = null;
if ($existingMetaData && is_array($existingMetaData)) {
    foreach ($existingMetaData as $record) {
        $metaDataList[$record['variable']] = $record;
    }
}

?>

<form id="metaDataForm" onsubmit="event.preventDefault();">
    <?php Csrf::printFormInputs("MetaData"); ?>
    <input id="id" class="form-control tab0" type="hidden" name="id" value="<?php echo $engineId; ?>"/>
    <div class="row padding-bottom-10">
        <div class="col-sm-4 col-xs-12 text-bold">
            <label class="control-label"><?php echo Session::t('Variable'); ?></label>
        </div>
        <div class="col-sm-8 col-xs-12">
            <label class="control-label"><?php echo Session::t('Value'); ?></label>
        </div>
    </div>
    <?php if ($edit && is_array($metaDataList)) {
        foreach ($metaDataList as $id => $record) {
        $variable = empty($metaDataList[$id]['variable']) ? null : htmlentities($metaDataList[$id]['variable']);
        $recordId = empty($metaDataList[$id]['id']) ? null : $metaDataList[$id]['id'];
        $value = empty($metaDataList[$id]['value']) ? null : $metaDataList[$id]['value']; ?>

        <input type="hidden" name="<?php echo $variable; ?>[id]" value="<?php echo $recordId; ?>"/>
        <div class="row padding-bottom-20">
            <div class="col-sm-4 col-xs-12">
                <input class="form-control" type="text" name="<?php echo $variable; ?>[variable]" value="<?php echo $variable; ?>"/>
            </div>
            <div class="col-sm-8 col-xs-12">
                <input class="form-control" type="text" name="<?php echo $variable; ?>[value]"  onfocus="cleanError()" value="<?php echo $value; ?>"/>
            </div>
        </div>
        <?php }
    } else { ?>
            <div class="row padding-bottom-20">
                <div class="col-sm-4 col-xs-12">
                    <input class="form-control tab0" type="text" placeholder="Variable" name="new<?php echo time(); ?>[variable]"  onfocus="cleanError()" value=""/>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <input class="form-control tab0" type="text" placeholder="Value" name="new<?php echo time(); ?>[value]"  onfocus="cleanError()" value=""/>
                </div>
            </div>

    <?php } ?>
    <div id="customMetaData">
    </div>
    <?php if (!$edit) { ?>
    <div class='row'>
        <div class='col-xs-12'>
            <div class='form-group-rule'>
                <button class='btn btn-default btn-sm pull-right' title="<?php echo Session::t('Add custom metadata'); ?>" name='button' id='btnAddCustomMetaData' value='+'><i class="fa fa-plus fa-green" aria-hidden="true"></i></button>
            </div>
        </div>
    </div>
    <?php } ?>
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
        window.customMetaData = 0;

        $("#btnSaveMetaData").click(function() {
            // Disable the finish button
            $('#btnSaveMetaData').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin').attr('disabled', 'disabled');

            $.post(
                "Ajax/metadata.php",
                $("#metaDataForm").serialize(),
                function(response) {
                    response = JSON.parse(response);
                    var message = response.message;
                    $(".modal .close").click();

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        if (window.tableMetadata != undefined) {
                            tableMetadata.ajax.reload();
                        }
                        <?php Helper::printSuccess('\' + message + \''); ?>
                    } else {
                        <?php Helper::printError('\' + message + \''); ?>
                    }
                }
            );

            return false;
        });

        $('#btnAddCustomMetaData').click(function(){
            customMetaData++;

            addRequestParameter();

            return false;
        });
    });

    function addRequestParameter() {
        var metaData = '<div class="row padding-bottom-20">' +
            '<div class="col-sm-4 col-xs-12">' +
            '<input class="form-control" type="text" placeholder="Variable" max="255" name="new' + customMetaData + '[variable]" value=""/>' +
            '</div>' +
            '<div class="col-sm-8 col-xs-12">' +
            '<input class="form-control" type="text" placeholder="Value" max="255" name="new' + customMetaData + '[value]" value=""/>' +
            '</div>';

        $("#customMetaData").append(metaData);

        return;
    }

</script>
