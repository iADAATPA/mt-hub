<?php

/**
 * Class Cache
 * @author Marek Mazur
 */
class Cache extends Database
{
    /**
     * Maximum number of chached segmnts per supplier
     */
    const CACHE_MAX = 200;

    /**
     * @var null|int
     */
    private $id = null;

    /**
     * @var null|int
     */
    private $supplierAccountId = null;

    /**
     * @var null|int
     */
    private $engineId = null;

    /**
     * @var null|array
     */
    private $segments = null;

    /**
     * @var null|array
     */
    private $translatedSegments = null;

    /**
     * @var null|int
     */
    private $domainId = null;

    /**
     * @var null|string
     */
    private $src = null;

    /**
     * @var null|string
     */
    private $trg = null;

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
                            domainid,
                            src,
                            trg,
                            segments,
                            translatedsegments,
                            time
                    )
                    VALUES (
                        :supplieraccountid,
                        :engineid,
                        :domainid,
                        :src,
                        :trg,
                        :segments,
                        :translatedsegments,
                        NOW()
                    )';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
        $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
        $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
        $this->bindValue(':src', $this->getSrc(), PDO::PARAM_STR);
        $this->bindValue(':trg', $this->getTrg(), PDO::PARAM_STR);
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
						domainid,
						src,
                        trg,
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
            $domain = $this->getDomainId() ? " = :domainid " : " IS NULL ";

            $query = 'SELECT
						translatedsegments
					FROM
						cache
					WHERE 
					    supplieraccountid = :supplieraccountid
                    AND 
                        segments          = :segments
                    AND   
                        engineid          = :engineid
                    AND 
                        domainid          ' . $domain . '
                    AND 
                        src               = :src
                    AND   
                        trg               = :trg';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':segments', $this->getSegments(), PDO::PARAM_STR);
            $this->bindValue(':engineid', $this->getEngineId(), PDO::PARAM_INT);
            if ($this->getDomainId()) {
                $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
            }

            $this->bindValue(':src', $this->getSrc(), PDO::PARAM_STR);
            $this->bindValue(':trg', $this->getTrg(), PDO::PARAM_STR);
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
                    domainid,
                    src,
                    trg,
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
            $this->setDomainId($details['domainid'] ? $details['domainid'] : null);
            $this->setSrc($details['src'] ? $details['src'] : null);
            $this->setTrg($details['trg'] ? $details['trg'] : null);
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

    /**
     * @return null
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param null $domainId
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
    }

    /**
     * @return null
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param null $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return null
     */
    public function getTrg()
    {
        return $this->trg;
    }

    /**
     * @param null $trg
     */
    public function setTrg($trg)
    {
        $this->trg = $trg;
    }
}
