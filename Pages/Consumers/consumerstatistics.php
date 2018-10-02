<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-pie-chart',
    'link' => 'Pages/Consumers/consumerstatistics.php',
    'pagename' => Session::t('Statistics')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('Statistics'),
    'breadcrumbs' => [
       $breadCrumb
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class"col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="dashboardTabs">
                <li class="active">
                    <a href="#generalStatistics" data-toggle="tab"><?php echo Session::t("General"); ?></a>
                </li>
                <li>
                    <a href="#monthlyStatistics" data-toggle="tab"><?php echo Session::t("Monthly"); ?></a>
                </li>
                <li>
                    <a href="#detailedStatistics" data-toggle="tab"><?php echo Session::t("Detailed"); ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="generalStatistics" class="tab-pane fade in active"></div>
                <div id="monthlyStatistics" class="tab-pane fade"></div>
                <div id="detailedStatistics" class="tab-pane fade"></div>
            </div>
        </div>
    </div>
    </div>
</section>

<script type="text/javascript">

    $(function() {
        var link = 'Pages/Consumers/Forms/statisticsgeneral.php';
        $("#generalStatistics").html(loader).load(link);

        $('[href="#generalStatistics"]').click(function() {
            $("#generalStatistics").html(loader).load(link);
        });

        $('[href="#monthlyStatistics"]').click(function(e) {
            if ($('[href="#monthlyStatistics"]').closest('li').hasClass("disabled")) {
                e.preventDefault();

                return false;
            } else {
                var link = 'Pages/Consumers/Forms/statisticsmonthly.php';
                $("#monthlyStatistics").html(loader).load(link);
            }
        });

        $('[href="#detailedStatistics"]').click(function(e) {
            if ($('[href="#detailedStatistics"]').closest('li').hasClass("disabled")) {
                e.preventDefault();

                return false;
            } else {
                var link = 'Datatables/statistics.php?type=<?php echo StatisticsSummary::TYPE_CONSUMER; ?>';
                $("#detailedStatistics").html(loader).load(link);
            }
        });
    });

</script>
