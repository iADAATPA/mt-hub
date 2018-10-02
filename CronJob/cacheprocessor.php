<?php

include_once '../functions.php';

// Make sure you have a cron job set on the server to run the script
// */6 * * * * curl https://iadaatpa.ie/CronJob/cacheprocessor.php &> /tmp/croncache.out

// Get all the requests form the db
$cache = new Cache();
// Get number of cache entries for every supplier
$suppliersCache = $cache->getCacheCountBySupplier();

if ($suppliersCache && is_array($suppliersCache)) {
    // If a number of entries is higher than the set limit removed older cache for every supplier
    foreach ($suppliersCache as $record) {
        $supplierAccountId = $record['supplieraccountid'];
        $cacheCount = $record['count'];

        if ($cacheCount > Cache::CACHE_MAX) {
            $cache->setSupplierAccountId($supplierAccountId);
            // Get the id of a x record and remove all previous recors.
            $ids = $cache->getLatestSupplierCacheIds(Cache::CACHE_MAX);
            $id = 0;

            if (!empty($ids) && is_array($ids)) {
                $latestElement = end($ids);
                $id = empty($latestElement["id"]) ? $id : $latestElement["id"];
            }

            $cache->setId($id);
            $cache->deleteOldCache();
        }
    }
}
