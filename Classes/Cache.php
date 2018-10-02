<?php

/**
 * Class Cache
 */
class Cache extends Database
{
    /**
     * Maximum number of chached segmnts per supplier
     */
    const CACHE_MAX = 200;

    private $id = null;
    private $supplierAccountId = null;
    private $engineId = null;
    private $segments = null;
    private $translatedSegments = null;

    /**
     * Cache constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        $query = 'INSERT INTO
                        cache(
                            supplieraccountid,
                            engineid,
                            segments,
                            translatedsegments,
                            time
                    )
                    VALUES (
                        :supplieraccountid,
                        :engineid,
                        :segments,
                        :translatedsegments,
                        NOW()
                    )';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
        $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
        $this->bindValue(':segments', $this->getSegments(), PDO::PARAM_STR);
        $this->bindValue(':translatedsegments', $this->getTranslatedSegments(), PDO::PARAM_STR);

        $this->execute();
        $id = $this->lastInsertId();
        $this->endTransaction();

        return $id;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        if (!empty($this->getId())) {
            $query = 'SELECT
                        id,
                        supplieraccountid,
						engineid,
						segments,
						translatedsegments
					FROM
						cache
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getCachedTranslatedSegments()
    {
        if (!empty($this->getSupplierAccountId())) {
            $query = 'SELECT
						translatedsegments
					FROM
						cache
					WHERE 
					    supplieraccountid = :supplieraccountid
                    AND 
                        segments          = :segments
                    AND   
                        engineid          = :engineid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':segments', $this->getSegments(), PDO::PARAM_STR);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            if ($result) {
                return json_decode($result['translatedsegments']);
            }
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        $query = 'SELECT
                    id,
                    supplieraccountid,
                    engineid,
                    segments,
                    translatedsegments
                FROM
                    cache';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        return $result;
    }

    /**
     * @return array|null
     */
    public function getCacheCountBySupplier()
    {
        $query = 'SELECT
                    COUNT(id) as count,
                    supplieraccountid
                FROM
                    cache
                GROUP BY
                    supplieraccountid';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        return $result;
    }

    /**
     * @param $limit
     * @return mixed
     */
    public function getLatestSupplierCacheIds($limit)
    {
        $query = 'SELECT
                      id
                  FROM
                      cache
                  WHERE 
                      supplieraccountid = :supplieraccountid
                  ORDER BY 
                      time DESC
                  LIMIT ' . $limit;

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
        $result = $this->resultSet();
        $this->endTransaction();

        return $result;
    }


    /**
     * @return bool|null
     */
    public function deleteOldCache()
    {
        if (!empty($this->getSupplierAccountId() && !empty($this->getId()))) {
            $query = 'DELETE FROM
                    cache
                WHERE 
                    supplieraccountid = :supplieraccountid
                AND 
                    id < :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @param array $details
     * @return bool
     */
    public function set($details)
    {
        if (!empty($details) && is_array($details)) {
            $this->setId($details['id'] ? $details['id'] : null);
            $this->setSupplierAccountId($details['supplieraccountid'] ? $details['supplieraccountid'] : null);
            $this->setEngineId($details['engineid'] ? $details['engineid'] : null);
            $this->setSegments($details['segments'] ? $details['segments'] : null);
            $this->setTranslatedSegments($details['translatedsegments'] ? $details['translatedsegments'] : null);

            return true;
        }

        return false;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getSupplierAccountId()
    {
        return $this->supplierAccountId;
    }

    /**
     * @param null $supplierAccountId
     */
    public function setSupplierAccountId($supplierAccountId)
    {
        $this->supplierAccountId = $supplierAccountId;
    }

    /**
     * @return null
     */
    public function getEngineId()
    {
        return $this->engineId;
    }

    /**
     * @param null $engineId
     */
    public function setEngineId($engineId)
    {
        $this->engineId = $engineId;
    }

    /**
     * @return null
     */
    public function getSegments()
    {

        return $this->segments;
    }

    /**
     * @param null $segments
     */
    public function setSegments($segments)
    {
        if (is_array($segments)) {
            $segments = json_encode($segments);
        }

        $this->segments = $segments;
    }

    /**
     * @return null
     */
    public function getTranslatedSegments()
    {
        return $this->translatedSegments;
    }

    /**
     * @param null $translatedSegments
     */
    public function setTranslatedSegments($translatedSegments)
    {
        if (is_array($translatedSegments)) {
            $translatedSegments = json_encode($translatedSegments);
        }

        $this->translatedSegments = $translatedSegments;
    }
}
