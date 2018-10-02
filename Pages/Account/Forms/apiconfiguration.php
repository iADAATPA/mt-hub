<?php

include_once '../../../functions.php';

Session::authenticateUser();

$urlConfig = new UrlConfig();
$urlConfig->setAccountId(Session::getAccountId());
$methodId = empty($_GET['methodId']) || !is_numeric($_GET['methodId']) ? null : $_GET['methodId'];
$id = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : $_GET['id'];

if (!$methodId && !$id) {
    die();
}

if ($methodId && !$id) {
    $urlConfig = new UrlConfig();
    $urlConfig->setAccountId(Session::getAccountId());
    $configurations = $urlConfig->getAllByMethodId();
    $id = empty($configurations[$methodId]['id']) ? $id : $configurations[$methodId]['id'];
}
$urlConfig = new UrlConfig($id);
$methodId = $id ? $urlConfig->getMethodId() : $methodId;

$methods = UrlConfig::getApiMethods();
$methodName = empty($methods[$methodId]) ? Session::t('Unknown') : $methods[$methodId];

?>

<div class="box-body">
    <div class="box-header">
        <h3 class="box-title"><?php echo strtoupper($methodName); ?></h3>
        <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('API Configuration'),
                'Help/apiconfiguration.php'); ?></span>
    </div>
    <div class="box-body">
        <form id="addUrlConfig<?php echo $methodId; ?>" onsubmit="event.preventDefault();">
            <?php Csrf::printFormInputs("UrlConfiguration" . $methodId); ?>
            <input type="hidden" name="methodId" value="<?php echo $methodId; ?>"/>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="hidden" name="accountId" value="<?php echo Session::getAccountId(); ?>"/>
            <div class="row padding-bottom-10">
                <div class="col-sm-6 col-xs-12">
                    <label class="control-label"><?php echo Session::t('URL End Point'); ?></label>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <label class="control-label"><?php echo Session::t('Type'); ?></label>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <label class="control-label"><?php echo Session::t('Authorization'); ?><?php Helper::printPopoverButton(Session::t('Authorization'),
                            Session::t('If you enable one of the authorization methods please make sure all you Consumers have set Username and Password.<br/><br/>Only one of the Authorization options will be accepted.')); ?></label>
                </div>
            </div>
            <div class="row padding-bottom-20">
                <div class="col-sm-6 col-xs-12">
                    <input class="form-control" max="255" name="endPoint" type="text"
                           value="<?php echo empty($urlConfig->getUrlEndPoint()) ? "" : $urlConfig->getUrlEndPoint() ?>"/>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <input <?php echo !in_array($methodId,
                        [UrlConfig::METHOD_RETRIEVE_FILE_TRANSLATION_ID]) ? 'disabled="disabled"' : ''; ?>
                            id="type<?php echo $methodId; ?>" name="type" checked="checked" data-width="100%"
                            data-height="34" data-toggle="toggle" type="checkbox"/>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <input id="basicAuth<?php echo $methodId; ?>"
                           <?php echo $urlConfig->getAuthorization() == UrlConfig::AUTH_BASIC ? 'checked="checked"' : ''; ?>name="basicAuth"
                           data-width="100%" data-height="34" data-toggle="toggle" type="checkbox"/>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <input id="digestAuth<?php echo $methodId; ?>"
                           <?php echo $urlConfig->getAuthorization() == UrlConfig::AUTH_DIGEST ? 'checked="checked"' : ''; ?>name="digestAuth"
                           data-width="100%" data-height="34" data-toggle="toggle" type="checkbox"/>
                </div>
            </div>
            <hr/>
            <div class="row padding-bottom-10">
                <div class="col-sm-12 col-xs-12">
                    <label class="control-label"><?php echo Session::t('Custom Headers'); ?><?php Helper::printPopoverButton(Session::t('Custom Headers'),
                            Session::t('Enter a valid JSON or leave it blank.<br/><br/>More details about the format you will find in the help window located on the top rigth corner of this page.')); ?></label>
                </div>
            </div>
            <div class="row padding-bottom-20">
                <div class="col-sm-12 col-xs-12">
                    <textarea id="header" name="header" class="form-control"
                              rows="5"><?php echo empty($urlConfig->getHeader()) ? "" : $urlConfig->getHeader(); ?></textarea>
                </div>
            </div>
            <hr/>
            <div class="row padding-bottom-10">
                <div class="col-sm-12 col-xs-12">
                    <label class="control-label"><?php echo Session::t('Request'); ?><?php Helper::printPopoverButton(Session::t('Request'),
                            Session::t('Enter a valid JSON.<br/><br/>More details about the format you will find in the help window located on the top rigth corner of this page.')); ?></label>
                </div>
            </div>
            <div class="row padding-bottom-20">
                <div class="col-sm-12 col-xs-12">
                    <textarea id="request" name="request" class="form-control"
                              rows="5"><?php echo empty($urlConfig->getRequest()) ? "" : $urlConfig->getRequest(); ?></textarea>
                </div>
            </div>
            <hr/>
            <?php if (in_array($methodId, [UrlConfig::METHOD_ATRANSLATE_ID, UrlConfig::METHOD_ATRANSLATE_FILE_ID])) { ?>
                <div class="row padding-bottom-10">
                    <div class="col-sm-12 col-xs-12">
                        <label class="control-label"><?php echo Session::t('Callback Url Paremeters'); ?><?php Helper::printPopoverButton(Session::t('Callback Url Paremeters'),
                                Session::t('Enter a valid JSON.<br/><br/>More details about the format you will find in the help window located on the top rigth corner of this page.')); ?></label>
                    </div>
                </div>
                <div class="row padding-bottom-20">
                    <div class="col-sm-12 col-xs-12">
                        <textarea id="callback" name="callback" class="form-control"
                                  rows="5"><?php echo empty($urlConfig->getCallback()) ? "" : $urlConfig->getCallback(); ?></textarea>
                    </div>
                </div>
                <hr/>
            <?php } ?>
            <div class="row padding-bottom-10">
                <div class="col-sm-12 col-xs-12">
                    <label class="control-label"><?php echo Session::t('Response (Path to translated segment/file or file guid)'); ?><?php Helper::printPopoverButton(Session::t('Response'),
                            Session::t('In the response field enter a path to traslated segments/file/guid in your Supplier API response.<br/><br/>More details about the format you will find in the help window located on the top rigth corner of this page.')); ?></label>
                </div>
            </div>
            <div class="row padding-bottom-10">
                <div class="col-sm-12 col-xs-12">
                    <input class="form-control" max="255" name="response" type="text"
                           value="<?php echo empty($urlConfig->getResponse()) ? "" : $urlConfig->getResponse(); ?>"/>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-xs-12">
                    <span class="formError error">&nbsp;</span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-footer">
                    <div class="btn-group">
                        <div id="cancel<?php echo $methodId; ?>" class="btn btn-default" data-bb-handler="cancel"
                             data-dismiss="modal"><i class="fa fa-times fa-fw"
                                                     aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Cancel'); ?></div>
                        <div id="btnSaveUrlConfig<?php echo $methodId; ?>" class="btn btn-info"><span
                                    id="saveUrlConfigIcon<?php echo $methodId; ?>"><i class="fa fa-floppy-o fa-fw"
                                                                                      aria-hidden="true"></i></span>&nbsp;&nbsp;<?php echo Session::t('Save'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $("#type<?php echo $methodId; ?>").bootstrapToggle({
            on: "Post",
            off: "Get",
            onstyle: 'success',
            offstyle: 'help'
        });

        $("#basicAuth<?php echo $methodId; ?>").bootstrapToggle({
            on: "Basic Auth On",
            off: "Basic Auth Off",
            onstyle: 'success',
            offstyle: 'help'
        });

        $("#digestAuth<?php echo $methodId; ?>").bootstrapToggle({
            on: "Digest Auth On",
            off: "Digest Auth Off",
            onstyle: 'success',
            offstyle: 'help'
        });
    });

    $("#btnSaveUrlConfig<?php echo $methodId; ?>").click(function () {
        $('#btnSaveUrlConfig<?php echo $methodId; ?>').find($('.fa')).removeClass('fa-floppy-o').addClass('fa-refresh fa-spin').prop('disabled', true);

        $.post(
            "Ajax/urlconfig.php",
            $("#addUrlConfig<?php echo $methodId; ?>").serialize(),
            function (response) {
                response = JSON.parse(response);
                var message = response.message;
                $('#btnSaveUrlConfig<?php echo $methodId; ?>').find($('.fa')).removeClass('fa-refresh fa-spin').addClass('fa-floppy-o').prop('disabled', false);

                if (response.statusId == '<?php echo ReturnCalls::STATUSID_SUCCESS; ?>') {
                    <?php Helper::printSuccess('\' + message + \''); ?>
                } else {
                    <?php Helper::printError('\' + message + \''); ?>
                }
            }
        );

        return false;
    });

</script>
