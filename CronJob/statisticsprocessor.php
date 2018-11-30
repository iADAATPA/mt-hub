<?php

include_once '../functions.php';

// Make sure you have a cron job set on the server to run the script
// */5 * * * * curl https://mt-hub.eu/CronJob/statisticsprocessor.php &> /tmp/cronstatistics.out

// Get all the requests form the db
$statisticsTemporary = new StatisticsTemporary();
$records = $statisticsTemporary->getAll();

if ($records && is_array($records)) {
    $summary = null;
    // Process each request
    foreach ($records as $record) {
        $recordId = $record['id'];
        $consumerAccountId = $record['consumeraccountid'];
        $supplierAccountId = $record['supplieraccountid'];
        $methodId = $record['methodid'];
        $engineId = $record['engineid'];
        $requestCount = $record['requestcount'];
        $wordCount = $record['wordcount'];
        $time = $record['time'];
        $lastFullHour = date("Y-m-d H:00:00", strtotime($time));

        if (empty($summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId])) {
            $summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId]['requestCount'] = $requestCount;
            $summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId]['wordCount'] = $wordCount;
            $summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId]['recordIds'][] = $recordId;
        } else {
            $summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId]['requestCount'] += $requestCount;
            $summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId]['wordCount'] += $wordCount;
            $summary[$lastFullHour][$consumerAccountId][$supplierAccountId][$methodId][$engineId]['recordIds'][] = $recordId;
        }
    }

    // Update the database
    if (!empty($summary) && is_array($summary)) {
        $statisticsSummary = new StatisticsSummary();

        foreach ($summary as $time => $timeDetails) {
            $statisticsSummary->setTime($time);

            foreach ($timeDetails as $consumerAccountId => $consumerDetails) {
                $statisticsSummary->setConsumerAccountId($consumerAccountId);

                foreach ($consumerDetails as $supplierAccountId => $supplierDetails) {
                    $statisticsSummary->setSupplierAccountId($supplierAccountId);

                    foreach ($supplierDetails as $methodId => $methodDetails) {
                        $statisticsSummary->setMethodId($methodId);

                        foreach ($methodDetails as $engineId => $engineDetails) {
                            $statisticsSummary->setEngineId($engineId);
                            $statisticsSummary->setWordCount($engineDetails['wordCount']);
                            $statisticsSummary->setRequestCount($engineDetails['requestCount']);
                            $result = $statisticsSummary->updateStatistics();

                            // If sucessfully updated remove the records from the statisticstemporary table
                            if ($result && !empty($engineDetails['recordIds'])) {
                                $statisticsTemporary->deleteRecords($engineDetails['recordIds']);
                            }
                        }
                    }
                }
            }
        }
    }
}
