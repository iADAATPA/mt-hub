<?php

include_once '../../functions.php';

Session::authenticateUser();

$header = [
    'title' => Session::t('Test Zone'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-connectdevelop',
            'link' => 'Pages/Suppliers/test.php',
            'pagename' => Session::t('Test Zone')
        ]
    ]
];

Helper::displayPageHeader($header);

$engines = new Engines();
$engines->setAccountId(Session::getAccountId());
$engineList = $engines->getAllAccountEngines();

$relations = new Relations();
$relations->setSupplierAccountId(Session::getAccountId());
$supplierConsumers = $relations->getSupplierConsumers();

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('Test'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Engine'); ?></label>
                            <select id="engineId" class="form-control" name="engineId" onchange="cleanForm()">
                                <?php foreach ($engineList as $key => $details) {
                                    if ($details['online']) {
                                        echo '<option id="' . $key . '" value="' . $key . '" data-source="' . $details['src'] . '" data-target="' . $details['trg'] . '" data-name="' . htmlentities($details['name']) . '">' . htmlentities($details['name']) . '</option>';
                                    }
                                } ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Consumer'); ?></label>
                            <select id="consumerId" class="form-control" name="consumerId" onchange="cleanForm()">
                                <?php if (is_array($supplierConsumers)) {
                                    foreach ($supplierConsumers as $key => $details) {
                                        echo '<option id="' . $details['consumeraccountid'] . '" value="' . $details['consumeraccountid'] . '">' . $details['name'] . '</option>';
                                    }
                                } ?>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Source'); ?></label>
                            <textarea id="sourceText" name="sourceText" class="form-control" rows="10" maxlength="1000"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-2 col-xs-12">
                        <div class="btn-group padding-top-20">
                            <div class="btn btn-default btn-block" id="btnTranslate"><i class="fa fa-language fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Translate'); ?></div>
                            <div class="btn btn-info btn-block" id="btnDetectLanguage"><i class="fa fa-globe fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Detect Language'); ?></div>
                            <div class="btn btn-warning btn-block" id="btnDetectDomain"><i class="fa fa-cubes fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Detect Domain'); ?></div>
                        </div>
                        <div class="btn-group padding-top-20">
                            <div class="btn btn-default btn-block" id="btnATranslate"><i class="fa fa-hourglass-start fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('aTranslate'); ?></div>
                            <div class="btn btn-default btn-block" id="btnARetrieveTranslation"><i class="fa fa-hourglass-end fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Retrieve Translation'); ?></div>
                        </div>
                    </div>
                    <div class="col-sm-5 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Formatted Response'); ?></label>
                            <textarea readonly id="responseText" class="form-control" rows="10"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row padding-top-10">
                    <div class="col-md-4">
                            <label class="control-label"><?php echo Session::t('HTTP Code'); ?></label>
                            <input class="form-control curl-info" id="httpCode" type="text" readonly />

                    </div>
                    <div class="col-md-4">
                            <label class="control-label"><?php echo Session::t('Total Time'); ?></label>
                            <input class="form-control curl-info" id="time" type="text" readonly />
                    </div>
                    <div class="col-md-4">
                            <label class="control-label"><?php echo Session::t('Request Size'); ?></label>
                            <input class="form-control curl-info" id="requestSize" type="text" readonly />

                    </div>
                </div>
                <div class="row padding-top-20">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Raw Response'); ?></label>
                            <textarea readonly id="responseRaw" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row padding-top-10">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Curl Info'); ?></label>
                            <textarea readonly id="curlInfo" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $(document).ready(function() {
        $("#engineId").select2({templateResult: formatEngineDisplay, templateSelection: formatEngineDisplay});
        $("#consumerId").select2();

        $("#btnDetectLanguage").click(function () {
            cleanForm();
            var sourceText = $("#sourceText").val();
            var customerId = $("#consumerId").val();
            $('#btnDetectLanguage').find($('.fa')).removeClass('fa-globe').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "detectLanguage",
                    customerId: customerId,
                    <?php Csrf::printParameters("DetectLanguage"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    message = response.message;
                    $("#responseRaw").val(message);
                    if (response.data) {
                        $("#curlInfo").val(response.data);
                        processCurlInfo(response.data);
                    }

                    $('#btnDetectLanguage').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-globe').prop('disabled', false);

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        message = JSON.parse(message);

                        if(!message.success) {
                            message = message.error.message;
                        } else {
                            var langName = message.data.language.name;
                            var langCode = message.data.language.code;
                            message = '{' + langCode + '} ' + langName;
                        }

                        $("#responseText").val(message);
                    } else {
                        $("#responseText").val('<?php echo Session::t('An unexpected error occurred'); ?>');
                    }
                }
            );
        });

        $("#btnDetectDomain").click(function () {
            cleanForm();
            var sourceText = $("#sourceText").val();
            var customerId = $("#consumerId").val();
            $('#btnDetectDomain').find($('.fa')).removeClass('fa-cubes').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "detectDomain",
                    source: null,
                    customerId: customerId,
                    <?php Csrf::printParameters("DetectDomain"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    message = response.message;
                    $("#responseRaw").val(message);
                    if (response.data) {
                        $("#curlInfo").val(response.data);
                        processCurlInfo(response.data);
                    }

                    $('#btnDetectDomain').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-cubes').prop('disabled', false);

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        message = JSON.parse(message);

                        if(!message.success) {
                            message = message.error.message;
                        } else {
                            message = message.data[0].domain;
                        }

                        $("#responseText").val(message);
                    } else {
                        $("#responseText").val('<?php echo Session::t('An unexpected error occurred'); ?>');
                    }
                }
            );
        });

        $("#btnTranslate").click(function () {
            cleanForm();
            var sourceText = $("#sourceText").val();
            var source = $("#engineId option:selected").attr("data-source");
            var target = $("#engineId option:selected").attr("data-target");
            var customerId = $("#consumerId").val();

            $('#btnTranslate').find($('.fa')).removeClass('fa-language').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "translate",
                    source: source,
                    target: target,
                    customerId: customerId,
                    <?php Csrf::printParameters("Translate"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    message = response.message;
                    $("#responseRaw").val(message);
                    if (response.data) {
                        $("#curlInfo").val(response.data);
                        processCurlInfo(response.data);
                    }

                    $('#btnTranslate').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-language').prop('disabled', false);

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        message = JSON.parse(message);

                        if(!message.success) {
                            message = JSON.stringify(message.error.message);
                        } else {
                            message = message.data.segments[0].translation;
                        }

                        $("#responseText").val(message);
                    } else {
                        $("#responseText").val('<?php echo Session::t('An unexpected error occurred'); ?>');
                    }
                }
            );
        });

        $("#btnATranslate").click(function () {
            cleanForm();
            var sourceText = $("#sourceText").val();
            var source = $("#engineId option:selected").attr("data-source");
            var target = $("#engineId option:selected").attr("data-target");
            var customerId = $("#consumerId").val();

            $('#btnATranslate').find($('.fa')).removeClass('fa-hourglass-start').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "aTranslate",
                    source: source,
                    target: target,
                    customerId: customerId,
                    <?php Csrf::printParameters("ATranslate"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    message = response.message;
                    $("#responseRaw").val(message);
                    if (response.data) {
                        $("#curlInfo").val(response.data);
                        processCurlInfo(response.data);
                    }

                    $('#btnATranslate').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-hourglass-start').prop('disabled', false);

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        message = JSON.parse(message);

                        if(!message.success) {
                            message = JSON.stringify(message.error.message);
                        } else {
                            message = message.data.guid;
                        }

                        $("#responseText").val(message);
                    } else {
                        $("#responseText").val('<?php echo Session::t('An unexpected error occurred'); ?>');
                    }
                }
            );
        });

        $("#btnARetrieveTranslation").click(function () {
            cleanForm();
            var sourceText = $("#sourceText").val();
            var source = $("#engineId option:selected").attr("data-source");
            var target = $("#engineId option:selected").attr("data-target");
            var customerId = $("#consumerId").val();

            $('#btnARetrieveTranslation').find($('.fa')).removeClass('fa-hourglass-end').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "aRetrieveTranslation",
                    source: source,
                    target: target,
                    customerId: customerId,
                    <?php Csrf::printParameters("aRetrieveTranslation"); ?>
                },
                function(response) {
                    response = JSON.parse(response);
                    message = response.message;
                    $("#responseRaw").val(message);
                    if (response.data) {
                        $("#curlInfo").val(response.data);
                        processCurlInfo(response.data);
                    }

                    $('#btnARetrieveTranslation').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-hourglass-end').prop('disabled', false);

                    if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                        message = JSON.parse(message);

                        if(!message.success) {
                            message = JSON.stringify(message.error.message);
                        } else {
                            message = message.data.segments[0].translation;
                        }

                        $("#responseText").val(message);
                    } else {
                        $("#responseText").val('<?php echo Session::t('An unexpected error occurred'); ?>');
                    }
                }
            );
        });
    });

    function cleanForm() {
        $("#responseText, #responseRaw, #curlInfo, .curl-info").val('');
    }

    function formatEngineDisplay(result) {
        if (!result.id) {
            return result.text;
        }

        if (!("element" in result)) {
            var sourceCode,  targetCode, engineName = '';
        } else {
            var sourceCode = result.element.dataset.source;
            var targetCode = result.element.dataset.target;
            var engineName = result.element.dataset.name;
        }


        var state = $("<div>" +
            "<span class='flag-icon flag-icon-" + sourceCode + "'></span>" +
            " => " +
            "<span class='flag-icon flag-icon-" + targetCode + "'></span>" +
            "<span style='text-align: left'>&nbsp;&nbsp;" + engineName + "</span>" +
            "</div>");

        return state;
    }

</script>