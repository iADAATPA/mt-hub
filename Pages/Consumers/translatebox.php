<?php

include_once '../../functions.php';

Session::authenticateUser();

$header = [
    'title' => Session::t('Translate Box'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-language',
            'link' => 'Pages/Consumers/translate.php',
            'pagename' => Session::t('Translate Box')
        ]
    ]
];

Helper::displayPageHeader($header);

$languages = new Languages();
$languageList = $languages->getAll();

$domains = new Domains();
$domains->setAccountId(Session::getAccountId());
$domainList = $domains->getAll();
$domainList = is_array($domainList) ? $domainList : [];

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('iADAATPA Translate'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Source Language'); ?></label>
                            <select id="source" class="form-control" onchange="cleanForm();">
                                <option id="0" selected value="0">&nbsp;Detect Language</option>
                                <?php foreach ($languageList as $language) {
                                    $code = $language['code'];
                                    $name = $language['name'];

                                    echo '<option id="' . $code . '" value="' . $code . '">&nbsp;' . $name . '    {' . $code . '}' . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Target Language'); ?></label>
                            <select id="target" class="form-control" onchange="cleanForm();">
                                <?php foreach ($languageList as $language) {
                                    $code = $language['code'];
                                    $name = $language['name'];
                                    $selected = 'en' == $code ? 'selected="selected"' : '';

                                    echo '<option id="' . $code . '" ' . $selected . ' value="' . $code . '">&nbsp;' . $name . '    {' . $code . '}' . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Domain'); ?></label>
                            <select id="domain" name="domain" class="form-control" onchange="cleanForm();">
                                <option id="0" selected value="0">&nbsp;Detect Domain</option>
                                <?php foreach ($domainList as $domain) {
                                    $id = $domain['id'];
                                    $name = $domain['name'];

                                    echo '<option id="' . htmlentities($name) . '" ' . $selected . ' value="' . $id . '">&nbsp;' . htmlentities($name) . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Source'); ?> [
                                <span id="sourceFlag" class="font-small"><span class="flag-icon"></span><span id="languageInput"></span></span>
                                </span><span id="languageInput"></span>
                                ]
                            </label>
                            <textarea id="sourceText" name="sourceText" class="form-control" rows="10" maxlength="1000"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-2 col-xs-12 center-content">
                        <div class="btn-group padding-top-20">
                            <div class="btn btn-warning btn-block" id="btnTranslate"><i class="fa fa-language fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Translate'); ?></div>
                            <div class="btn btn-default btn-block" id="btnATranslate"><i class="fa fa-hourglass-start fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('aTranslate'); ?></div>
                            <div class="btn btn-default btn-block" id="btnARetrieveTranslation"><i class="fa fa-hourglass-end fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Retrieve Translation'); ?></div>
                        </div>
                    </div>
                    <div class="col-sm-5 col-xs-12">
                        <div class="form-group">
                            <label class="control-label"><?php echo Session::t('Translation'); ?> [ <span id="targetFlag" class="font-small"><span class="flag-icon flag-icon-en"></span></span> ]</label>
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
                            <label class="control-label"><?php echo Session::t('Raw iADAATPA Response'); ?></label>
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

    function cleanForm() {
        $("#responseText, #requestSize, #responseRaw, #time, #curlInfo, #httpCode").val('');
    }

    $(document).ready(function() {
        window.detectDomain = true;
        window.detectLanguage = true;

        $("#source, #target").select2({templateResult: formatState});
        $("#domain").select2();

        $("#source").change(function(event, triggeredBy) {
            if (triggeredBy !== "system") {
                var langCode = $("#source").val();
                $('#sourceFlag').find($('.flag-icon')).removeClass(function (index, className) {
                    return (className.match (/\bflag-icon-\S+/g) || []).join(' ');
                }).addClass('flag-icon-' + langCode);
                $("#languageInput").text('');

                detectLanguage = langCode == 0 ? true : false;
            }
        });

        $("#target").change(function() {
            var langCode = $("#target").val();
            $('#targetFlag').find($('.flag-icon')).removeClass(function (index, className) {
                return (className.match (/\bflag-icon-\S+/g) || []).join(' ');
            }).addClass('flag-icon-' + langCode);
        });

        $("#domain").change(function(event, triggeredBy) {
            if (triggeredBy !== "system") {
                var source = $("#source").val();
                detectDomain = source == 0 ? true : false;
            }
        });

        $("#sourceText").bind("input", function () {
            var domainId = $("#domain").val();
            var source = $("#source").val();
            var sourceText = $("#sourceText").val();

            try {
                window.stop();
            } catch (exception) {
                document.execCommand('Stop');
            }

            if (source == 0 || detectLanguage) {
                $.post(
                    "Ajax/testapi.php",
                    {
                        segments: sourceText,
                        method: "detectLanguage",
                        customerId: <?php echo Session::getAccountId(); ?>,
                        <?php Csrf::printParameters("DetectLanguage"); ?>
                    },
                    function(response) {
                        response = JSON.parse(response);
                        message = response.message;

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            message = JSON.parse(message);

                            if(message.success &&  message.data.language.code) {
                                var langCode = message.data.language.code;
                                $('#sourceFlag').find($('.flag-icon')).removeClass(function (index, className) {
                                    return (className.match (/\bflag-icon-\S+/g) || []).join(' ');
                                }).addClass('flag-icon-' + langCode);
                                $("#languageInput").text(' Detected');
                                $("#source").val(langCode).attr("selected", "selected").trigger( "change", [ "system"] );
                            }
                        }
                    }
                );
            }

            if (domainId == 0 || detectDomain) {
                $.post(
                    "Ajax/testapi.php",
                    {
                        segments: sourceText,
                        method: "detectDomain",
                        source: null,
                        customerId: <?php echo Session::getAccountId(); ?>,
                        <?php Csrf::printParameters("DetectDomain"); ?>
                    },
                    function(response) {
                        response = JSON.parse(response);
                        message = response.message;

                        if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                            message = JSON.parse(message);

                            if(message.success && message.data[0].domain) {
                                var domain = message.data[0].domain;
                                $("#domain option[id='" + domain + "']").attr("selected", "selected").trigger( "change", [ "system"] );
                            }
                        }
                    }
                );
            }
        });

        $("#btnTranslate").click(function () {
            cleanForm();
            var sourceText = $("#sourceText").val();
            var target = $("#target").val();
            var source = $("#source").val();
            var domain = $("#domain").val();

            $('#btnTranslate').find($('.fa')).removeClass('fa-language').addClass('fa-refresh fa-spin').prop('disabled', true);

            try {
                window.stop();
            } catch (exception) {
                document.execCommand('Stop');
            }

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "translate",
                    source: source,
                    domain: domain,
                    target: target,
                    customerId: <?php echo Session::getAccountId(); ?>,
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
            var target = $("#target").val();
            var source = $("#source").val();

            $('#btnATranslate').find($('.fa')).removeClass('fa-hourglass-start').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "aTranslate",
                    source: source,
                    target: target,
                    customerId: <?php echo Session::getAccountId(); ?>,
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
            var target = $("#target").val();
            var source = $("#source").val();

            $('#btnARetrieveTranslation').find($('.fa')).removeClass('fa-hourglass-end').addClass('fa-refresh fa-spin').prop('disabled', true);

            $.post(
                "Ajax/testapi.php",
                {
                    segments: sourceText,
                    method: "aRetrieveTranslation",
                    source: source,
                    target: target,
                    customerId: <?php echo Session::getAccountId(); ?>,
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

</script>
