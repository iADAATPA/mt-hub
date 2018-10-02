<?php

include_once '../../../functions.php';

Session::authenticateUser();

$currentYearMonth = date("Y-m", time());
$yearMonth = empty($_GET['time']) ? $currentYearMonth : $_GET['time'];
$year = substr($yearMonth, 0, 4);

$statisticsSummary = new StatisticsSummary();
$statisticsSummary->setConsumerAccountId(Session::getAccountId());
$statisticsSummary->generateConsumerStatistics();
$monthlyWordCount = $statisticsSummary->getMonthlyWordCount();
$currentMonthWordCount = empty($monthlyWordCount[$yearMonth]) ? 0 : $monthlyWordCount[$yearMonth];
$monthlyRequestCount = $statisticsSummary->getMonthlyRequestCount();
$currentMonthRequestCount = empty($monthlyRequestCount[$yearMonth]) ? 0 : $monthlyRequestCount[$yearMonth];
$monthlyEngines = $statisticsSummary->getMonthlyEngines();
$currentMonthEngineCount = empty($monthlyEngines[$yearMonth]) ? 0 : count($monthlyEngines[$yearMonth]);
$monthlySuppliers = $statisticsSummary->getMonthlySuppliers();
$currentMonthSupplierCount = empty($monthlySuppliers[$yearMonth]) ? 0 : count($monthlySuppliers[$yearMonth]);
$monthlyEngineWordCount = $statisticsSummary->getMonthylEngineWordCount();
$currentMonthEngineWordCount = empty($monthlyEngineWordCount[$yearMonth]) ? 0 : $monthlyEngineWordCount[$yearMonth];
$monthlyEngineRequestCount = $statisticsSummary->getMonthylEngineRequestCount();
$currentMonthEngineRequestCount = empty($monthlyEngineRequestCount[$yearMonth]) ? 0 : $monthlyEngineRequestCount[$yearMonth];

$dailyWordCount = $statisticsSummary->getDailyWordCount();
$dailyRequestCount = $statisticsSummary->getDailyRequestCount();

$currentMonthDailyWordCount = empty($dailyWordCount[$yearMonth]) ? [] : $dailyWordCount[$yearMonth];
$currentMonthDailyRequestCount = empty($dailyRequestCount[$yearMonth]) ? [] : $dailyRequestCount[$yearMonth];

$data = [];
$labels = null;
$numberOfDaysInAMonth = date('t', strtotime($yearMonth));

for ($i = 1; $i <= $numberOfDaysInAMonth; $i++) {
    $formattedDay = sprintf('%02d', $i);
    $labels[] = $formattedDay;
    $data['Word Count'][] = empty($currentMonthDailyWordCount[$yearMonth . "-" . $formattedDay]) ? 0 : $currentMonthDailyWordCount[$yearMonth . "-" . $formattedDay];
    $data['Request Count'][] = empty($currentMonthDailyRequestCount[$yearMonth . "-" . $formattedDay]) ? 0 : $currentMonthDailyRequestCount[$yearMonth . "-" . $formattedDay];
}

$engines = new Engines();
$engineList = $engines->getAll();

$dataEngines = [];
$labelsEngines = null;
if (!empty($currentMonthEngineWordCount) && is_array($currentMonthEngineWordCount)) {
    foreach ($currentMonthEngineWordCount as $engineId => $wordCount) {
        $labelsEngines[] = empty($engineList[$engineId]['name']) ? $engineId : $engineList[$engineId]['name'];
        $dataEngines['Word Count'][] = $wordCount;
    }
}

if (!empty($currentMonthEngineRequestCount) && is_array($currentMonthEngineRequestCount)) {
    foreach ($currentMonthEngineRequestCount as $engineId => $requestCount) {
        $dataEngines['Request Count'][] = $requestCount;
    }
}

$charts = new Charts('monthly');
$charts->setPlaceHolderHeight(115);
$charts->setShowDoubleAxes(true);
$charts->setTitle(Session::t("Word and Request Count for " . date("F Y", strtotime($yearMonth))));
$charts->setLabels($labels);
$charts->setData($data);

$chartsEngines = new Charts('monthlyEngines');
$chartsEngines->setShowDoubleAxes(true);
$chartsEngines->setTitle(Session::t("Engine Word and Request Count for " . date("F Y", strtotime($yearMonth))));
$chartsEngines->setLabels($labelsEngines);
$chartsEngines->setData($dataEngines);

$accounts = new Accounts(Session::getAccountId());
$accountCreated = $optionYearMonth = substr($accounts->getCreated(), 0, 7);

$yearMonths[date("Y-m", strtotime($accountCreated))] = date("F Y", strtotime($accountCreated));

while ($currentYearMonth != $optionYearMonth) {
    $nextMonth = strtotime('next month', strtotime($optionYearMonth));
    $optionYearMonth = date("Y-m", $nextMonth);
    $yearMonths[$optionYearMonth] = date(" F Y", $nextMonth);
}

$yearMonths = array_reverse($yearMonths);

?>

<div class="box-body">
    <div class="box-header">
        <h3 class="box-title"><?php echo Session::t("Monthly Statistics"); ?></h3>
        <span class="pull-right btn-help-circle"><?php Helper::printHelpButton(Session::t('Monthly Statistics'), 'Help/statistics.php'); ?></span>
    </div>
    <div class="box-body">
        <div class="row padding-bottom-20">
            <div class="col-sm-3 col-xs-12">
                <div class="info-box bg-grey">
                    <span class="info-box-icon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text padding-top-5"><?php echo Session::t('Selected Month'); ?></span>
                            <select id="yearMonth" class="form-control" name="yearMonth" onchange="updateMonthlyStatistics();">
                                <?php foreach ($yearMonths as $yearMonthYm => $yearMonthFY) {
                                    $selected = $yearMonthYm == $yearMonth ? 'selected="selected"' : '';
                                    echo '<option value="' . $yearMonthYm . '" ' . $selected . '>' . $yearMonthFY . '</option>';
                                } ?>
                            </select>
                    </div>
                </div>
                <div class="info-box bg-yellow">
                   <span class="info-box-icon"><i class="fa fa-calculator" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Word Count'); ?></span>
                       <span class="info-box-number"><?php echo number_format($currentMonthWordCount); ?></span>
                   </div>
                </div>
                <div class="info-box bg-green">
                   <span class="info-box-icon"><i class="fa fa-handshake-o" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Request Count'); ?></span>
                       <span class="info-box-number"><?php echo number_format($currentMonthRequestCount); ?></span>
                   </div>
                </div>
                <div class="info-box bg-blue">
                   <span class="info-box-icon"><i class="fa fa-qrcode" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Suppliers Used'); ?></span>
                       <span class="info-box-number"><?php echo number_format($currentMonthSupplierCount); ?></span>
                   </div>
                </div>
                <div class="info-box bg-red">
                   <span class="info-box-icon"><i class="fa fa-th" aria-hidden="true"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text"><?php echo Session::t('Engines Used'); ?></span>
                       <span class="info-box-number"><?php echo number_format($currentMonthEngineCount); ?></span>
                   </div>
                </div>
            </div>
            <div class="col-sm-9 col-xs-12">
                <div class="col-sm-12 col-xs-12">
                <?php $chartsEngines->printPlaceHolder(); ?>
                </div>
            </div>
        </div>
        <div class="row padding-bottom-10">
            <div class="col-sm-12 col-xs-12">
                <?php $charts->printPlaceHolder(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $("#yearMonth").select2();
    });
    
    function updateMonthlyStatistics() {
        var selectedMonth = $("#yearMonth").val();

        var link = 'Pages/Consumers/Forms/statisticsmonthly.php?time=';
        $("#monthlyStatistics").html(loader).load(link + selectedMonth);
    }

</script>

<?php

$charts->printScript();
$chartsEngines->printScript();

?>
