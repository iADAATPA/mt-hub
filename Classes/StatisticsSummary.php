<?php

/**
 * Class StatisticsSummary
 * @author Marek Mazur
 */
class StatisticsSummary extends Statistics
{
    /**
     * Database table
     */
    const TABLE_NAME = "statisticssummary";

    /**
     * Statistic types
     */
    const TYPE_SUPPLIER = "Supplier";
    const TYPE_CONSUMER = "Consumer";

    /**
     * @var null|int
     */
    private $generalUsedSuppliers = 0;

    /**
     * @var null|int
     */
    private $generalUsedConsumers = 0;

    /**
     * @var null|int
     */
    private $generalUsedEngines = 0;

    /**
     * @var null|int
     */
    private $generalUsedMethods = 0;

    /**
     * @var null|int
     */
    private $generalRequestCount = 0;

    /**
     * @var null|int
     */
    private $generalWordCount = 0;

    /**
     * @var null|int
     */
    private $generalEngineRequestCount = 0;

    /**
     * @var null|int
     */
    private $generalEngineWordCount = 0;

    /**
     * @var null|int
     */
    private $monthlyWordCount = null;

    /**
     * @var null|int
     */
    private $monthlyRequestCount = null;

    /**
     * @var null|int
     */
    private $monthlyEngines = null;

    /**
     * @var null|int
     */
    private $monthlySuppliers = null;

    /**
     * @var null|int
     */
    private $dailyWordCount = null;

    /**
     * @var null|int
     */
    private $dailyRequestCount = null;

    /**
     * @var null|int
     */
    private $monthylEngineWordCount = null;

    /**
     * @var null|int
     */
    private $monthylEngineRequestCount = null;

    /**
     * StatisticsSummary constructor.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        // Set table to statisticstemporary
        $this->setTable(self::TABLE_NAME);

        if ($id) {
            $this->setId($id);
            $this->set($this->get());
        }
    }

    /**
     * Updated statistics summary
     *
     * @return mixed|null
     */
    public function updateStatistics()
    {
        //First lets check if we have stats for given time, consumerAccountId, supplierAccountId, methodId, and engineId
        $query = 'SELECT
                        id,
                        consumeraccountid,
                        supplieraccountid,
                        methodid,
                        engineid,
                        requestcount,
                        wordcount,
                        time
					FROM
						' . $this->getTable() . '
					WHERE 
					    time                = :time
                    AND
					    consumeraccountid   = :consumeraccountid
                    AND
                        supplieraccountid   = :supplieraccountid
                    AND
                        methodid            = :methodid
                    AND
                        engineid            = :engineid';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
        $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
        $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
        $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
        $this->bindValue(':time', $this->getTime(), PDO::PARAM_STR);
        $result = $this->result();
        $this->endTransaction();

        if ($result) {
            $this->setId($result['id']);
            $this->setWordCount($this->getWordCount() + $result['wordcount']);
            $this->setRequestCount($this->getRequestCount() + $result['requestcount']);
            $result = $this->update();
        } else {
            $result = $this->insert();
        }

        return $result;
    }

    /**
     * Get consumer statistics
     *
     * @return mixed|null
     */
    private function getConsumerStatistics()
    {
        if (!empty($this->getConsumerAccountId())) {
            $query = 'SELECT
                    id,
                    consumeraccountid,
                    supplieraccountid,
                    methodid,
                    engineid,
                    requestcount,
                    wordcount,
                    time
                FROM
                    ' . $this->getTable() . '
                WHERE
                    consumeraccountid   = :consumeraccountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * Get supplier statistics
     *
     * @return mixed|null
     */
    private function getSupplierStatistics()
    {
        if (!empty($this->getSupplierAccountId())) {
            $query = 'SELECT
                    id,
                    consumeraccountid,
                    supplieraccountid,
                    methodid,
                    engineid,
                    requestcount,
                    wordcount,
                    time
                FROM
                    ' . $this->getTable() . '
                WHERE
                    supplieraccountid   = :supplieraccountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * Generate consumer statistics
     */
    public function generateConsumerStatistics()
    {
        $result = $this->getConsumerStatistics();
        $this->generateStatistics($result);
    }

    /**
     * Generate supplier statistics
     */
    public function generateSupplierStatistics()
    {
        $result = $this->getSupplierStatistics();
        $this->generateStatistics($result);
    }

    /**
     * Generates and sets all statistics
     */
    private function generateStatistics($result)
    {
        if ($result && is_array($result)) {
            $summary = [
                'supplierAccountId' => [],
                'consumerAccountId' => [],
                'engineId' => [],
                'methodId' => [],
                'requestCount' => 0,
                'wordCount' => 0,
                'engineWordCount' => [],
                'engineRequestCount' => [],
                'monthlyWordCount' => [],
                'monthlyRequestCount' => [],
                'monthlyEngines' => [],
                'monthylEngineWordCount' => [],
                'monthylEngineRequestCount' => [],
                'monthlySuppliers' => [],
                'dailyWordCount' => [],
                'dailyRequestCount' => []
            ];

            // Sumarise data
            foreach ($result as $record) {
                $time = $record['time'];
                $month = substr($time, 0, 7);
                $monthDay = substr($time, 0, 10);

                $summary['supplierAccountId'][$record['supplieraccountid']] = $record['supplieraccountid'];
                $summary['consumerAccountId'][$record['consumeraccountid']] = $record['consumeraccountid'];
                $summary['engineId'][$record['engineid']] = $record['engineid'];
                $summary['requestCount'] += $record['requestcount'];
                $summary['wordCount'] += $record['wordcount'];
                $summary['methodId'][$record['methodid']] = $record['methodid'];
                $summary['engineWordCount'][$record['engineid']] = empty($summary['engineWordCount'][$record['engineid']]) ? $record['wordcount'] : $summary['engineWordCount'][$record['engineid']] + $record['wordcount'];
                $summary['engineRequestCount'][$record['engineid']] = empty($summary['engineRequestCount'][$record['engineid']]) ? $record['requestcount'] : $summary['engineRequestCount'][$record['engineid']] + $record['requestcount'];
                $summary['monthlyWordCount'][$month] = empty($summary['monthlyWordCount'][$month]) ? $record['wordcount'] : $summary['monthlyWordCount'][$month] + $record['wordcount'];
                $summary['monthlyRequestCount'][$month] = empty($summary['monthlyRequestCount'][$month]) ? $record['requestcount'] : $summary['monthlyRequestCount'][$month] + $record['requestcount'];
                $summary['monthlyEngines'][$month][$record['engineid']] = $record['engineid'];
                $summary['monthlySuppliers'][$month][$record['supplieraccountid']] = $record['supplieraccountid'];
                $summary['dailyWordCount'][$month][$monthDay] = empty($summary['dailyWordCount'][$month][$monthDay]) ? $record['wordcount'] : $summary['dailyWordCount'][$month][$monthDay] + $record['wordcount'];
                $summary['dailyRequestCount'][$month][$monthDay] = empty($summary['dailyRequestCount'][$month][$monthDay]) ? $record['requestcount'] : $summary['dailyRequestCount'][$month][$monthDay] + $record['requestcount'];
                $summary['monthylEngineWordCount'][$month][$record['engineid']] = empty($summary['monthylEngineWordCount'][$month][$record['engineid']]) ? $record['wordcount'] : $summary['monthylEngineWordCount'][$month][$record['engineid']] + $record['wordcount'];
                $summary['monthylEngineRequestCount'][$month][$record['engineid']] = empty($summary['monthylEngineRequestCount'][$month][$record['engineid']]) ? $record['requestcount'] : $summary['monthylEngineRequestCount'][$month][$record['engineid']] + $record['requestcount'];
            }

            $this->setGeneralUsedSuppliers(is_array($summary['supplierAccountId']) ? count($summary['supplierAccountId']) : 0);
            $this->setGeneralUsedConsumers(is_array($summary['consumerAccountId']) ? count($summary['consumerAccountId']) : 0);
            $this->setGeneralUsedEngines(is_array($summary['engineId']) ? count($summary['engineId']) : 0);
            $this->setGeneralRequestCount($summary['requestCount']);
            $this->setGeneralWordCount($summary['wordCount']);
            $this->setGeneralUsedMethods(is_array($summary['methodId']) ? count($summary['methodId']) : 0);
            $this->setGeneralEngineRequestCount($summary['engineRequestCount']);
            $this->setGeneralEngineWordCount($summary['engineWordCount']);
            $this->setMonthlyWordCount($summary['monthlyWordCount']);
            $this->setMonthlyRequestCount($summary['monthlyRequestCount']);
            $this->setMonthlyEngines($summary['monthlyEngines']);
            $this->setMonthlySuppliers($summary['monthlySuppliers']);
            $this->setDailyWordCount($summary['dailyWordCount']);
            $this->setDailyRequestCount($summary['dailyRequestCount']);
            $this->setMonthylEngineWordCount($summary['monthylEngineWordCount']);
            $this->setMonthylEngineRequestCount($summary['monthylEngineRequestCount']);
        }
    }

    /**
     * @return int
     */
    public function getGeneralUsedSuppliers()
    {
        return $this->generalUsedSuppliers;
    }

    /**
     * @param int $generalUsedSuppliers
     */
    public function setGeneralUsedSuppliers($generalUsedSuppliers)
    {
        $this->generalUsedSuppliers = $generalUsedSuppliers;
    }

    /**
     * @return int
     */
    public function getGeneralUsedEngines()
    {
        return $this->generalUsedEngines;
    }

    /**
     * @param int $generalUsedEngines
     */
    public function setGeneralUsedEngines($generalUsedEngines)
    {
        $this->generalUsedEngines = $generalUsedEngines;
    }

    /**
     * @return int
     */
    public function getGeneralUsedMethods()
    {
        return $this->generalUsedMethods;
    }

    /**
     * @param int $generalUsedMethods
     */
    public function setGeneralUsedMethods($generalUsedMethods)
    {
        $this->generalUsedMethods = $generalUsedMethods;
    }

    /**
     * @return int
     */
    public function getGeneralRequestCount()
    {
        return $this->generalRequestCount;
    }

    /**
     * @param int $generalRequestCount
     */
    public function setGeneralRequestCount($generalRequestCount)
    {
        $this->generalRequestCount = $generalRequestCount;
    }

    /**
     * @return int
     */
    public function getGeneralWordCount()
    {
        return $this->generalWordCount;
    }

    /**
     * @param int $generalWordCount
     */
    public function setGeneralWordCount($generalWordCount)
    {
        $this->generalWordCount = $generalWordCount;
    }

    /**
     * @return int
     */
    public function getGeneralEngineRequestCount()
    {
        return $this->generalEngineRequestCount;
    }

    /**
     * @param int $generalEngineRequestCount
     */
    public function setGeneralEngineRequestCount($generalEngineRequestCount)
    {
        $this->generalEngineRequestCount = $generalEngineRequestCount;
    }

    /**
     * @return int
     */
    public function getGeneralEngineWordCount()
    {
        return $this->generalEngineWordCount;
    }

    /**
     * @param int $generalEngineWordCount
     */
    public function setGeneralEngineWordCount($generalEngineWordCount)
    {
        $this->generalEngineWordCount = $generalEngineWordCount;
    }

    /**
     * @return null
     */
    public function getMonthlyWordCount()
    {
        return $this->monthlyWordCount;
    }

    /**
     * @param null $monthlyWordCount
     */
    public function setMonthlyWordCount($monthlyWordCount)
    {
        $this->monthlyWordCount = $monthlyWordCount;
    }

    /**
     * @return null
     */
    public function getMonthlyRequestCount()
    {
        return $this->monthlyRequestCount;
    }

    /**
     * @param null $monthlyRequestCount
     */
    public function setMonthlyRequestCount($monthlyRequestCount)
    {
        $this->monthlyRequestCount = $monthlyRequestCount;
    }

    /**
     * @return null
     */
    public function getMonthlyEngines()
    {
        return $this->monthlyEngines;
    }

    /**
     * @param null $monthlyEngines
     */
    public function setMonthlyEngines($monthlyEngines)
    {
        $this->monthlyEngines = $monthlyEngines;
    }

    /**
     * @return null
     */
    public function getMonthlySuppliers()
    {
        return $this->monthlySuppliers;
    }

    /**
     * @param null $monthlySuppliers
     */
    public function setMonthlySuppliers($monthlySuppliers)
    {
        $this->monthlySuppliers = $monthlySuppliers;
    }

    /**
     * @return null
     */
    public function getDailyWordCount()
    {
        return $this->dailyWordCount;
    }

    /**
     * @param null $dailyWordCount
     */
    public function setDailyWordCount($dailyWordCount)
    {
        $this->dailyWordCount = $dailyWordCount;
    }

    /**
     * @return null
     */
    public function getDailyRequestCount()
    {
        return $this->dailyRequestCount;
    }

    /**
     * @param null $dailyRequestCount
     */
    public function setDailyRequestCount($dailyRequestCount)
    {
        $this->dailyRequestCount = $dailyRequestCount;
    }

    /**
     * @return null
     */
    public function getMonthylEngineWordCount()
    {
        return $this->monthylEngineWordCount;
    }

    /**
     * @param null $monthylEngineWordCount
     */
    public function setMonthylEngineWordCount($monthylEngineWordCount)
    {
        $this->monthylEngineWordCount = $monthylEngineWordCount;
    }

    /**
     * @return null
     */
    public function getMonthylEngineRequestCount()
    {
        return $this->monthylEngineRequestCount;
    }

    /**
     * @param null $monthylEngineRequestCount
     */
    public function setMonthylEngineRequestCount($monthylEngineRequestCount)
    {
        $this->monthylEngineRequestCount = $monthylEngineRequestCount;
    }

    /**
     * @return int
     */
    public function getGeneralUsedConsumers()
    {
        return $this->generalUsedConsumers;
    }

    /**
     * @param int $generalUsedConsumers
     */
    public function setGeneralUsedConsumers($generalUsedConsumers)
    {
        $this->generalUsedConsumers = $generalUsedConsumers;
    }
}
