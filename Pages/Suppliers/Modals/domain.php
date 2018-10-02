<?php

include_once '../../../functions.php';

Session::authenticateUser();

$domainId = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : (int)$_GET['id'];
$domains = new Domains($domainId);

if ($domainId && (Session::getAccountId() != $domains->getAccountId())) {
    exit(Session::t('You are unauthorized to see this page'));
}

$name = $domains->getName() ? $domains->getName() : '';
$source = $domains->getSrc() ? $domains->getSrc() : Languages::LANGUAGE_EN;
$languages = new Languages();
$languageList = $languages->getAll();

$isDomainAssigned = false;
if ($domainId) {
    $engines = new Engines();
    $engines->setDomainId($domainId);
    $isDomainAssigned = $engines->isDomainAssigned();
}

?>

<form id="formAddDomain" onsubmit="event.preventDefault();">
    <?php Csrf::printFormInputs("Domain"); ?>
    <?php if ($domainId) { ?>
        <input id="id" class="form-control tab0" type="hidden" name="id" value="<?php echo $domainId; ?>"/>
    <?php } ?>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Domain Name'); ?></label>
                <input id="name" class="form-control tab0" type="text" name="name" onfocus="cleanError()" autofocus value="<?php echo $name; ?>"/>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Source Language'); ?></label>
                <select id="source" class="form-control tab1" name="source" onchange="cleanError();">

                    <?php foreach ($languageList as $language) {
                        $code = $language['code'];
                        $name = $language['name'];
                        $selected = $source == $code? 'selected="selected"' : '';

                        echo '<option id="' . $code . '" ' . $selected . ' value="' . $code . '">&nbsp;' . $name . '    {' . $code . '}' . '</option>';
                    } ?>

                </select>
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
                <?php if ($isDomainAssigned){ ?>
                <div class="btn btn-info" disabled="disabled" title="<?php echo Session::t("Domain assigned to an engine can't be modified."); ?>"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
                <?php } else { ?>
                <div id="btnSaveDomain" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function() {
        $("#source").select2({templateResult: formatState});
        $('#name').focus();
    });

    $("#btnSaveDomain").click(function() {
        var name = $('#name').val();

        if (name == "") {
            $('#error').html('<?php echo Session::t('No Domain name.'); ?>');
        } else {
            saveDomain();
        }

        return false;
    });

    function saveDomain() {
        $('#btnSaveDomain').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin').attr('disabled', 'disabled');

        $.post(
            "Ajax/domain.php",
            $("#formAddDomain").serialize(),
            function(response) {
                response = JSON.parse(response);
                var message = response.message;
                $(".modal .close").click();

                if (window.tableAllDomains != undefined) {
                    tableAllDomains.ajax.reload();
                }

                if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    }

</script>
