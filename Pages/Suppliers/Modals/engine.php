<?php

include_once '../../../functions.php';

Session::authenticateUser();

$engineId = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : (int)$_GET['id'];
$admin = empty($_GET['admin']) ?  null : $_GET['admin'];
$engines = new Engines($engineId);

if ($engineId && (Session::getAccountId() != $engines->getAccountId() && !Session::isAdministrator())) {
    exit(Session::t('You are unauthorized to see this page'));
}

$name = $engines->getName() ? htmlentities($engines->getName()) : '';
$description = $engines->getDescription() ? htmlentities($engines->getDescription()) : '';
$customId = $engines->getCustomId() ? htmlentities($engines->getCustomId()) : '';
$source = $engines->getSource() ? $engines->getSource() : Languages::LANGUAGE_EN;
$target = $engines->getTarget() ? $engines->getTarget() : Languages::LANGUAGE_EN;
$type = $engines->getType() ? $engines->getType() : Engines::ENGINE_TYPE_SMT;
$online = empty($engineId) ? true : $engines->getOnline();
$bleu = empty($engines->getBleu()) ? 0 : $engines->getBleu();
$ter = empty($engines->getTer()) ? 0 : $engines->getTer();
$fmeasure = empty($engines->getFmeasure()) ? 0 : $engines->getFmeasure();
$costPerWord = empty($engines->getCostPerWord()) ? '' : $engines->getCostPerWord();
$trainingWordCount = empty($engines->getTrainingWordCount()) ? '' : $engines->getTrainingWordCount();

$languages = new Languages();
$languageList = $languages->getAll();

$domains = new Domains();
$domains->setAccountId(Session::getAccountId());
$domainList = $domains->getAccountDomains();
$domainList = is_array($domainList) ? $domainList : [];

$types = [
    Engines::ENGINE_TYPE_SMT => 'SMT',
    Engines::ENGINE_TYPE_NMT => 'NMT',
    Engines::ENGINE_TYPE_RBMT => 'RBMT'
];

if ($admin && Session::isAdministrator()) {
    $accounts = new Accounts();
    $accountList = $accounts->getAll();
}

?>


<form id="addengine" onsubmit="event.preventDefault();">
    <?php Csrf::printFormInputs("AddEngine"); ?>
    <?php if ($engineId) { ?>
        <input id="id" class="form-control" type="hidden" name="id" value="<?php echo $engineId; ?>"/>
    <?php } ?>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Engine Name'); ?>*</label>
                <input id="name" class="form-control" type="text" name="name" onfocus="cleanError()" autofocus
                       value="<?php echo htmlentities($name); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Domain'); ?></label>
                <select id="domainId" class="form-control" name="domainId" tabindex="-1" onchange="cleanError();">

                    <?php foreach ($domainList as $domain) {
                        $id = $domain['id'];
                        $name = htmlentities($domain['name']);
                        $selected = $engines->getDomainId() == $id ? 'selected="selected"' : '';

                        echo '<option id="' . $id . '" ' . $selected . ' value="' . $id . '">&nbsp;' . $name . '</option>';
                    } ?>

                </select>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Status'); ?></label>
                <input id="online" name="online" <?php echo $online ? 'checked="checked"' : ''; ?> data-width="100%"
                       data-height="34" data-toggle="toggle" type="checkbox"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Source Language'); ?>*</label>
                <select id="source" class="form-control" name="source" onchange="cleanError();">

                    <?php foreach ($languageList as $language) {
                        $code = $language['code'];
                        $name = htmlentities($language['name']);
                        $selected = $source == $code ? 'selected="selected"' : '';

                        echo '<option id="' . $code . '" ' . $selected . ' value="' . $code . '">&nbsp;' . $name . '    {' . $code . '}' . '</option>';
                    } ?>

                </select>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Target Language'); ?>*</label>
                <select id="target" class="form-control" name="target" tabindex="-1" onchange="cleanError();">

                    <?php foreach ($languageList as $language) {
                        $code = $language['code'];
                        $name = htmlentities($language['name']);
                        $selected = $target == $code ? 'selected="selected"' : '';

                        echo '<option id="' . $code . '" ' . $selected . ' value="' . $code . '">&nbsp;' . $name . '    {' . $code . '}' . '</option>';
                    } ?>

                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Cost Per Word'); ?></label>
                <input id="costperword" name="costperword" class="form-control" type="text" maxlength="12"
                       value="<?php echo $costPerWord; ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Training Word Count'); ?></label>
                <input id="trainingwordcount" name="trainingwordcount" class="form-control" type="text" maxlength="12"
                       value="<?php echo $trainingWordCount; ?>"/>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label
                    class="control-label"><?php echo Session::t('Custom Id'); ?><?php Helper::printPopoverButton(Session::t('Custom Id'), Session::t('This is an optional input.<br/>The custom Id might be used in the API Configuration settings as a Supplier request parameter. <br/>For example it can be used to pass engine\'s systemID or mtsystem name')); ?></label>
                <input class="form-control" type="text" max="125" name="customId" onfocus="cleanError()" autofocus
                       value="<?php echo htmlentities($customId); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label
                    class="control-label">Type<?php Helper::printPopoverButton(Session::t('Engine Types'), Session::t('Select between NMT and SMT.')); ?></label>
                <select id="type" class="form-control" name="type" onchange="cleanError();">

                    <?php foreach ($types as $typeId => $typeDesc) {
                        $selected = $type == $typeId ? 'selected="selected"' : '';

                        echo '<option id="' . $typeId . '" value="' . $typeId . '" ' . $selected . '>' . $typeDesc . '</option>';
                    } ?>

                </select>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('TER'); ?> <sup>(<span id="terVal"><?php echo $ter; ?></span>%)</sup></label>
                <input id="ter" name="ter" class="form-control" type="text" data-slider-min="0" data-slider-max="100"
                       data-slider-step="1" data-slider-value="<?php echo $ter; ?>" data-slider-tooltip="hide"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('BLEU'); ?> <sup>(<span
                            id="bleuVal"><?php echo $bleu; ?></span>%)</sup></label>
                <input id="bleu" name="bleu" class="form-control" type="text" data-slider-min="0" data-slider-max="100"
                       data-slider-step="1" data-slider-value="<?php echo $bleu; ?>" data-slider-tooltip="hide"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('F-Measure'); ?> <sup>(<span
                            id="fmeasureVal"><?php echo $fmeasure; ?></span>%)</sup></label>
                <input id="fmeasure" name="fmeasure" class="form-control" type="text" data-slider-min="0"
                       data-slider-max="100" data-slider-step="1" data-slider-value="<?php echo $fmeasure; ?>"
                       data-slider-tooltip="hide"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="control-label"><?php echo Session::t('Description'); ?></label>
                <input id="description" class="form-control" type="text" maxlength="255" name="description"
                       onfocus="cleanError()" autofocus value="<?php echo htmlentities($description); ?>"/>
            </div>
        </div>
    </div>
    <?php if ($admin && Session::isAdministrator()) { ?>
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label class="control-label"><?php echo Session::t('Supplier'); ?></label>
                    <select id="accountId" class="form-control" name="accountId" onchange="cleanError();">

                        <?php foreach ($accountList as $account) {
                            $selected = $account['id'] == $engines->getAccountId() ? 'selected="selected"' : '';

                            if (in_array($account['groupid'], [Groups::GROUP_ADMINISTRATOR, Groups::GROUP_SUPPLIER])) {
                                echo '<option id="' . $account['id'] . '" ' . $selected . ' value="' . $account['id'] . '">[' . $account['id'] . '] ' . $account['name'] . '</option>';
                            }
                        } ?>

                    </select>
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
                <div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?>
                </div>
                <div id="btnSaveEngine" class="btn btn-info"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Save'); ?></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function() {
        $("#source, #target").select2({templateResult: formatState});
        $("#type, #accountId, #domainId").select2();
        $('#name').focus();

        $("#online").bootstrapToggle({
            on: "<?php echo Session::t('Online'); ?>",
            off: "<?php echo Session::t('Offline'); ?>",
            onstyle: 'success',
            offstyle: 'danger'
        });

        var sliderTer = new Slider("#ter");
        sliderTer.on("slide", function (sliderValue) {
            document.getElementById("terVal").textContent = sliderValue;
        });

        var sliderBleu = new Slider("#bleu");
        sliderBleu.on("slide", function (sliderValue) {
            document.getElementById("bleuVal").textContent = sliderValue;
        });

        var sliderFmeasure = new Slider("#fmeasure");
        sliderFmeasure.on("slide", function (sliderValue) {
            document.getElementById("fmeasureVal").textContent = sliderValue;
        });
    });

    $("#btnSaveEngine").click(function () {
        var name = $('#name').val();
        console.log(name);
        var source = $('#source').val();
        var target = $('#target').val();

        if (name == "") {
            $('#error').html('<?php echo Session::t('No engine name.'); ?>');
        } else if (source == target) {
            $('#error').html('<?php echo Session::t('Same source and target language'); ?>');
        } else {
            saveEngine();
        }

        return false;
    });

    function saveEngine() {
        $('#btnSaveEngine').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin').attr('disabled', 'disabled');

        $.post("Ajax/engine.php",$("#addengine").serialize(),function (response) {
                response = JSON.parse(response);
                var message = response.message;
                $(".modal .close").click();

                if (response.statusId == "<?php echo ReturnCalls::STATUSID_SUCCESS; ?>") {
                    if (window.tableEngines != undefined) {
                        tableEngines.ajax.reload();
                    }

                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );
    }

</script>
