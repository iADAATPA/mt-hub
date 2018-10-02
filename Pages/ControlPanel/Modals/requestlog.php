<?php

include_once '../../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$id = empty($_GET['id']) || !is_numeric($_GET['id']) ? null : (int)$_GET['id'];

$requestLog = new RequestLog($id);
$methodList = UrlConfig::getApiMethods();
$engines = new Engines($requestLog->getEngineId());
$consumerAccount = new Accounts($requestLog->getConsumerAccountId());
$supplierAccount = new Accounts($requestLog->getSupplierAccountId());

?>

<form id="formAccount" onsubmit="event.preventDefault();">
	<div class="row">
        <div class="col-sm-3 col-xs-12">
        	<div class="form-group">
        		<label class="control-label" for="method"><?php echo Session::t('Method'); ?></label>
				<input class="form-control" type="text" readonly value="<?php echo empty($methodList[$requestLog->getMethodId()]) ? "N/A" : $methodList[$requestLog->getMethodId()]; ?>"/>
        	</div>
       	</div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="httpCode"><?php echo Session::t('HTTP Code'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getHttpCode(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="consumerId"><?php echo Session::t('Consumer Id'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getConsumerAccountId(); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="consumerName"><?php echo Session::t('Consumer Name'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $consumerAccount->getName(); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="supplierId"><?php echo Session::t('Supplier Id'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getSupplierAccountId(); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="supplierName"><?php echo Session::t('Supplier Name'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $supplierAccount->getName(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="engineId"><?php echo Session::t('Engine Id'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getEngineId(); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="engineName"><?php echo Session::t('Engine Name'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $engines->getName(); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="src"><?php echo Session::t('Source'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getSrc(); ?>"/>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="trg"><?php echo Session::t('Target'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getTrg(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="timeIn"><?php echo Session::t('Time In'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getTimeIn(); ?>"/>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="timeOut"><?php echo Session::t('Time Out'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getTimeOut(); ?>"/>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="timeMs"><?php echo Session::t('Time (ms)'); ?></label>
                <input class="form-control" type="text" readonly value="<?php echo $requestLog->getTimeMs(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="request"><?php echo Session::t('Request'); ?></label>
                <textarea class="form-control" rows="4" readonly><?php echo $requestLog->getRequest(); ?></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="control-label" for="response"><?php echo Session::t('Respone'); ?></label>
                <textarea class="form-control" rows="4" readonly><?php echo $requestLog->getResponse(); ?></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
        	<span id="accountError" class="formError error">&nbsp;</span>
        </div>
    </div>
    <div class="row">
		<div class="col-xs-12 form-footer">
      		<div class="btn-group">
      			<div class="btn btn-default" data-bb-handler="cancel" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Close'); ?></div>
            </div>
        </div>
   	</div> 
</form>
