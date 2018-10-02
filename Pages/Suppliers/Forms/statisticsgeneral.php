<?php

include_once '../../../functions.php';

Session::authenticateUser();

$yearMonth = empty($_GET['time']) ? date("Y-m", time()) : $_GET['time'];
$year = substr($yearMonth, 0, 4);

$statisticsSummary = new StatisticsSummary();
$statisticsSummary->setSupplierAccountId(Session::getAccountId());
$statisticsSummary->generateSupplierStatistics();
$generalEngineWordCount = $statisticsSummary->getGeneralEngineWordCount();
$generalEngineRequestCount = $statisticsSummary->getGeneralEngineRequestCount();
$monthlyWordCount = $statisticsSummary->getMonthlyWordCount();
$monthlyRequestCount = $statisticsSummary->getMonthlyRequestCount();

$engines = new Engines();
$engineList = $engines->getAll();

$data = [];
$labels = null;
if (!empty($generalEngineWordCount) && is_array($generalEngineWordCount)) {
    foreach ($generalEngineWordCount as $engineId => $wordCount) {
        $labels[] = empty($engineList[$engineId]['name']) ? $engineId : $engineList[$engineId]['name'];
        $data['Word Count'][] = $wordCount;
    }
}

if (!empty($generalEngineRequestCount) && is_array($generalEngineRequestCount)) {
    foreach ($generalEngineRequestCount as $engineId => $requestCount) {
        $data['Request Count'][] = $requestCount;
    }
}

$dataMonthly = [];
$labelsMonthly = null;
$year = date("Y");

for ($m = 1; $m <= 12; $m++) {
    $dateTime = mktime(0, 0, 0, $m, 1, $year);
    $labelsMonthly[] = date('F', $dateTime);
    $chartYearMonth = date("Y-m", $dateTime);
    $dataMonthly['Word Count'][] = empty($monthlyWordCount[$chartYearMonth]) ? 0 : $monthlyWordCount[$chartYearMonth];
    $dataMonthly['Request Count'][] = empty($monthlyRequestCount[$chartYearMonth]) ? 0 : $monthlyRequestCount[$chartYearMonth];

}

$charts = new Charts('general');
$charts->setShowDoubleAxes(true);
$charts->setTitle(Session::t("Engine Total Word and Request Count"));
$charts->setLabels($labels);
$charts->setData($data);

$chartsMonthly = new Charts('yearly');
$chartsMonthly->setPlaceHolderHeight(115);
$chartsMonthly->setShowDoubleAxes(true);
$chartsMonthly->setTitle(Session::t("Word and Request Count for " . date("Y", strtotime($yearMonth))));
$chartsMonthly->setLabels($labelsMonthly);
$chartsMonthly->setData($dataMonthly);

$accounts = new Accounts(Session::getAccountId());
$accountCreated = date("F Y", strtotime($accounts->getCreated()));

?>

<div class="box-body">
    <div class="box-header">
        <h3 class="box-title"><?php echo Session::t("General Statistics"); ?></h3>
        <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('General Statistics'), 'Help/statistics.php'); ?></span>
    </div>
    <div class="box-body">
        <div class="row padding-bottom-20">
            <div class="col-sm-3 col-xs-12">
                <div class="info-box bg-grey">
                    <span class="info-box-icon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text padding-top-5"><?php echo Session::t('Statistics from'); ?></span>
                        <input disabled="disabled" class="form-control" value="<?php echo $accountCreated; ?>" />
                    </div>
                </div>
                <div class="info-box bg-yellow">
                   <span class="info-box-icon"><i class="fa fa-calculator" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Word Count'); ?></span>
                       <span class="info-box-number"><?php echo number_format($statisticsSummary->getGeneralWordCount()); ?></span>
                   </div>
                </div>
                <div class="info-box bg-green">
                   <span class="info-box-icon"><i class="fa fa-handshake-o" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Request Count'); ?></span>
                       <span class="info-box-number"><?php echo number_format($statisticsSummary->getGeneralRequestCount()); ?></span>
                   </div>
                </div>
                <div class="info-box bg-blue">
                   <span class="info-box-icon"><i class="fa fa-qrcode" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Consumers'); ?></span>
                       <span class="info-box-number"><?php echo number_format($statisticsSummary->getGeneralUsedConsumers()); ?></span>
                   </div>
                </div>
                <div class="info-box bg-red">
                   <span class="info-box-icon"><i class="fa fa-th" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Engines Used'); ?></span>
                       <span class="info-box-number"><?php echo number_format($statisticsSummary->getGeneralUsedEngines()); ?></span>
                   </div>
                </div>
            </div>
            <div class="col-sm-9 col-xs-12">
                <?php $charts->printPlaceHolder(); ?>
            </div>
        </div>
        <div class="box-body">
            <div class="row padding-bottom-10">
                <div class="col-sm-12 col-xs-12">
                    <?php $chartsMonthly->printPlaceHolder(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $("#yearMonthGeneral").select2();
    });

    function updateYearlyStatistics() {
        var selectedMonth = $("#yearMonthGeneral").val();

        var link = 'Pages/Consumers/Forms/statisticsgeneral.php?time=';
        $("#generalStatistics").html(loader).load(link + selectedMonth);
    }

</script>

<?php

$charts->printScript();
$chartsMonthly->printScript();

?>
