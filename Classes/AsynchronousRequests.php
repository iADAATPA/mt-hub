<?php

/**
 * Class AsynchronousRequests
 * @package Classes
 */
class AsynchronousRequests extends Database
{
    private $id = null;
    private $consumeraccountid = null;
    private $supplierAccountId = null;
    private $engineName = null;
    private $engineCustomId = null;
    private $src = null;
    private $trg = null;
    private $domain = null;
    private $requestTime = null;
    private $text = null;
    private $translation = null;
    private $translationTime = null;
    private $status = null;
    private $retry = null;
    private $error = null;
    private $methodId = UrlConfig::METHOD_TRANSLATE_ID;
    private $fileType = null;
    private $supplierGuid = null;

    /**
     * Asynchronous Requests constructor.
     * @param null|int $id
     */
    public function __construct($id = null)
    {
        if ($id) {
            $this->setId($id);
            $this->set($this->get());
        }
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if (!empty($this->getConsumerAccountid())) {
            $query = 'INSERT INTO
							asynchronousrequests(
							    id,
							    consumeraccountid,
							    supplieraccountid,
							    status,
							    enginename,
							    enginecustomid,
							    src,
							    trg,
							    domain,
							    text,
							    translation,
							    translationtime,
							    requesttime,
							    retry,
							    error,
							    methodid,
							    filetype,
							    supplierguid
						)
						VALUES (
						    :id,
                            :consumeraccountid,
                            :supplieraccountid,
                            :status,
                            :enginename,
                            :enginecustomid,
                            :src,
                            :trg,
                            :domain,
                            :text,
                            :translation,
                            :translationtime,
                            :requesttime,
                            :retry,
                            :error,
                            :methodid,
                            :filetype,
                            :supplierguid
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_STR);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountid(), PDO::PARAM_INT);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':status', $this->getStatus(), PDO::PARAM_INT);
            $this->bindValue(':enginename', $this->getEngineName(), PDO::PARAM_STR);
            $this->bindValue(':enginecustomid', $this->getEngineCustomId(), PDO::PARAM_STR);
            $this->bindValue(':src', $this->getSource(), PDO::PARAM_STR);
            $this->bindValue(':trg', $this->getTarget(), PDO::PARAM_STR);
            $this->bindValue(':domain', $this->getDomain(), PDO::PARAM_STR);
            $this->bindValue(':text', $this->getText(), PDO::PARAM_STR);
            $this->bindValue(':translation', $this->getTranslation(), PDO::PARAM_STR);
            $this->bindValue(':translationtime', $this->getTranslationTime(), PDO::PARAM_STR);
            $this->bindValue(':requesttime', $this->getRequestTime(), PDO::PARAM_STR);
            $this->bindValue(':retry', $this->getRetry(), PDO::PARAM_INT);
            $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
            $this->bindValue(':error', $this->getError(), PDO::PARAM_STR);
            $this->bindValue(':filetype', $this->getFileType(), PDO::PARAM_STR);
            $this->bindValue(':supplierguid', $this->getSupplierGuid(), PDO::PARAM_STR);
            $response = $this->execute();
            $this->endTransaction();

            if ($response) {
                return $this->getId();
            }
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        if (!empty($this->getId())) {
            $query = 'SELECT
                        id,
						consumeraccountid,
                        supplieraccountid,
                        status,
                        enginename,
                        enginecustomid,
                        src,
                        trg,
                        domain,
                        text,
                        translation,
                        translationtime,
                        requesttime,
                        retry,
                        error,
                        methodid,
                        filetype,
                        supplierguid
					FROM
						asynchronousrequests
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_STR);
            $result = $this->result();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function delete()
    {
        if (!empty($this->getId())) {
            $query = 'DELETE FROM
						asynchronousrequests
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_STR);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
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
                    consumeraccountid,
                    supplieraccountid,
                    status,
                    enginename,
                    enginecustomid,
                    src,
                    trg,
                    domain,
                    text,
                    translation,
                    translationtime,
                    requesttime,
                    retry,
                    error,
                    methodid,
                    filetype,
                    supplierguid
                FROM
                    asynchronousrequests';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $asynchronousrequests = [];

            foreach ($result as $row) {
                $asynchronousrequests[$row['id']] = $row;
            }

            return $asynchronousrequests;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId()) {
            $query = 'UPDATE
							asynchronousrequests
						SET
							consumeraccountid   = :consumeraccountid,
                            supplieraccountid   = :supplieraccountid,
                            status              = :status,
                            enginename          = :enginename,
                            enginecustomid      = :enginecustomid,
                            src                 = :src,
                            trg                 = :trg,
                            domain              = :domain,
                            text                = :text,
                            translation         = :translation,
                            translationtime     = :translationtime,
                            requesttime         = :requesttime,
                            retry               = :retry,
                            error               = :error,
                            methodid            = :methodid,
                            filetype            = :filetype,
                            supplierguid        = :supplierguid
						WHERE
							id			        = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_STR);
            $this->bindValue(':consumeraccountid', $this->getConsumerAccountid(), PDO::PARAM_INT);
            $this->bindValue(':supplieraccountid', $this->getSupplierAccountId(), PDO::PARAM_INT);
            $this->bindValue(':status', $this->getStatus(), PDO::PARAM_INT);
            $this->bindValue(':enginename', $this->getEngineName(), PDO::PARAM_STR);
            $this->bindValue(':enginecustomid', $this->getEngineCustomId(), PDO::PARAM_STR);
            $this->bindValue(':src', $this->getSource(), PDO::PARAM_STR);
            $this->bindValue(':trg', $this->getTarget(), PDO::PARAM_STR);
            $this->bindValue(':domain', $this->getDomain(), PDO::PARAM_STR);
            $this->bindValue(':text', $this->getText(), PDO::PARAM_STR);
            $this->bindValue(':translation', $this->getTranslation(), PDO::PARAM_STR);
            $this->bindValue(':translationtime', $this->getTranslationTime(), PDO::PARAM_STR);
            $this->bindValue(':requesttime', $this->getRequestTime(), PDO::PARAM_STR);
            $this->bindValue(':retry', $this->getRetry(), PDO::PARAM_INT);
            $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
            $this->bindValue(':error', $this->getError(), PDO::PARAM_STR);
            $this->bindValue(':filetype', $this->getFileType(), PDO::PARAM_STR);
            $this->bindValue(':supplierguid', $this->getSupplierGuid(), PDO::PARAM_STR);
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
            $this->setConsumerAccountid($details['consumeraccountid'] ? $details['consumeraccountid'] : null);
            $this->setSupplierAccountId($details['supplieraccountid'] ? $details['supplieraccountid'] : null);
            $this->setStatus($details['status'] ? $details['status'] : null);
            $this->setEngineName($details['enginename'] ? $details['enginename'] : null);
            $this->setEngineCustomId($details['enginecustomid'] ? $details['enginecustomid'] : null);
            $this->setSource($details['src'] ? $details['src'] : null);
            $this->setTarget($details['trg'] ? $details['trg'] : null);
            $this->setDomain($details['domain'] ? $details['domain'] : null);
            $this->setText($details['text'] ? $details['text'] : null);
            $this->setTranslation($details['translation'] ? $details['translation'] : null);
            $this->setTranslationTime($details['translationtime'] ? $details['translationtime'] : null);
            $this->setRequestTime($details['requesttime'] ? $details['requesttime'] : null);
            $this->setRetry($details['retry'] ? $details['retry'] : null);
            $this->setError($details['error'] ? $details['error'] : null);
            $this->setMethodId($details['methodid'] ? $details['methodid'] : null);
            $this->setFileType($details['filetype'] ? $details['filetype'] : null);
            $this->setSupplierGuid($details['supplierguid'] ? $details['supplierguid'] : null);

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
    public function getConsumerAccountid()
    {
        return $this->consumeraccountid;
    }

    /**
     * @param null $consumeraccountid
     */
    public function setConsumerAccountid($consumeraccountid)
    {
        if (is_numeric($consumeraccountid)) {
            $this->consumeraccountid = $consumeraccountid;
        }
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
        if (is_numeric($supplierAccountId)) {
            $this->supplierAccountId = $supplierAccountId;
        }
    }

    /**
     * @return null
     */
    public function getEngineName()
    {
        return $this->engineName;
    }

    /**
     * @param null $engineName
     */
    public function setEngineName($engineName)
    {
        $this->engineName = $engineName;
    }

    /**
     * @return null
     */
    public function getEngineCustomId()
    {
        return $this->engineCustomId;
    }

    /**
     * @param null $engineCustomId
     */
    public function setEngineCustomId($engineCustomId)
    {
        $this->engineCustomId = $engineCustomId;
    }

    /**
     * @return null
     */
    public function getSource()
    {
        return $this->src;
    }

    /**
     * @param null $src
     */
    public function setSource($src)
    {
        $this->src = $src;
    }

    /**
     * @return null
     */
    public function getTarget()
    {
        return $this->trg;
    }

    /**
     * @param null $trg
     */
    public function setTarget($trg)
    {
        $this->trg = $trg;
    }

    /**
     * @return null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param null $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return null
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * @param null $requestTime
     */
    public function setRequestTime($requestTime)
    {
        $this->requestTime = $requestTime;
    }

    /**
     * @return null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param null $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return null
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @param null $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
    }

    /**
     * @return null
     */
    public function getTranslationTime()
    {
        return $this->translationTime;
    }

    /**
     * @param null $translationTime
     */
    public function setTranslationTime($translationTime)
    {
        $this->translationTime = $translationTime;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param null $status
     */
    public function setStatus($status)
    {
        if (is_numeric($status)) {
            $this->status = $status;
        }
    }

    /**
     * @return null
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * @param null $retry
     */
    public function setRetry($retry)
    {
        if (is_numeric($retry)) {
            $this->retry = $retry;
        }
    }

    /**
     * @return null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param null $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getMethodId()
    {
        return $this->methodId;
    }

    /**
     * @param int $methodId
     */
    public function setMethodId($methodId)
    {
        $this->methodId = $methodId;
    }

    /**
     * @return null
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param null $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }

    /**
     * @return null
     */
    public function getSupplierGuid()
    {
        return $this->supplierGuid;
    }

    /**
     * @param null $supplierGuid
     */
    public function setSupplierGuid($supplierGuid)
    {
        $this->supplierGuid = $supplierGuid;
    }
}
