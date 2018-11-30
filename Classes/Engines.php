<?php

/**
 * Class Engines
 */
class Engines extends Database
{
    private $id = null;
    private $name = null;
    private $accountId = null;
    private $trg = null;
    private $src = null;
    private $type = null;
    private $created = null;
    private $deleted = null;
    private $online = null;
    private $customId = null;
    private $domainId = null;
    private $ter = null;
    private $bleu = null;
    private $fmeasure = null;
    private $trainingWordCount = null;
    private $costPerWord = null;
    private $description = null;

    const ENGINE_TYPE_SMT = 1;
    const ENGINE_TYPE_NMT = 2;
    const ENGINE_TYPE_RBMT = 3;

    /**
     * Engines constructor.
     * @param null $id
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
        if (!empty($this->getName())) {
            $query = 'INSERT INTO
							engines(
								name,
								accountid,
								trg,
								src,
								domainid,
								online,
								type,
								created,
								customid,
								ter,
								bleu,
								fmeasure,
								trainingwordcount,
								costperword,
								description
						)
						VALUES (
                            :name,
                            :accountid,
                            :trg,
                            :src,
                            :domainid,
                            :online,
                            :type,
                            NOW(),
                            :customid,
                            :ter,
                            :bleu,
                            :fmeasure,
                            :trainingwordcount,
                            :costperword,
                            :description
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
            $this->bindValue(':trg', $this->getTarget(), PDO::PARAM_STR);
            $this->bindValue(':src', $this->getSource(), PDO::PARAM_STR);
            $this->bindValue(':customid', $this->getCustomId(), PDO::PARAM_STR);
            $this->bindValue(':ter', $this->getTer(), PDO::PARAM_STR);
            $this->bindValue(':bleu', $this->getBleu(), PDO::PARAM_STR);
            $this->bindValue(':fmeasure', $this->getFmeasure(), PDO::PARAM_STR);
            $this->bindValue(':costperword', $this->getCostPerWord(), PDO::PARAM_STR);
            $this->bindValue(':online', $this->getOnline(), PDO::PARAM_INT);
            $this->bindValue(':type', $this->getType(), PDO::PARAM_INT);
            $this->bindValue(':trainingwordcount', $this->getTrainingWordCount(), PDO::PARAM_STR);
            $this->bindValue(':description', $this->getDescription(), PDO::PARAM_STR);
            $this->execute();
            $id = $this->lastInsertId();
            $this->endTransaction();

            return $id;
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
                        name,
                        accountid,
                        trg,
                        src,
                        domainid,
                        online,
                        type,
                        created,
                        deleted,
                        customid,
                        ter,
                        bleu,
                        fmeasure,
                        trainingwordcount,
                        costperword,
                        description
					FROM
						engines
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
     * @return array|null
     */
    public function getAll()
    {
        $query = 'SELECT
                    id,
                    name,
                    accountid,
                    trg,
                    src,
                    domainid,
                    online,
                    type,
                    created,
                    deleted,
                    customid,
                    ter,
                    bleu,
                    fmeasure,
                    trainingwordcount,
                    costperword,
                    description
                FROM
                    engines';

        $this->startTransaction();
        $this->query($query);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $engines = [];

            foreach ($result as $row) {
                $engines[$row['id']] = $row;
            }

            return $engines;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAllConsumerEngines($consumerId)
    {
        $query = 'SELECT
                    id,
                    name,
                    accountid,
                    trg,
                    src,
                    domainid,
                    online,
                    type,
                    created,
                    deleted,
                    customid,
                    ter,
                    bleu,
                    fmeasure,
                    trainingwordcount,
                    costperword,
                    description
                FROM
                    engines
                WHERE
                    accountid 
                IN (SELECT 
                      supplieraccountid 
                  FROM 
                      relations 
                  WHERE 
                      consumeraccountid = :consumeraccountid)';

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':consumeraccountid', $consumerId, PDO::PARAM_INT);
        $result = $this->resultSet();
        $this->endTransaction();

        if ($result) {
            $engines = [];

            foreach ($result as $row) {
                $engines[$row['id']] = $row;
            }

            return $engines;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAllAccountEngines()
    {
        if ($this->getAccountId()) {
            $query = 'SELECT
                    id,
                    name,
                    accountid,
                    trg,
                    src,
                    domainid,
                    online,
                    type,
                    created,
                    deleted,
                    customid,
                    ter,
                    bleu,
                    fmeasure,
                    trainingwordcount,
                    costperword,
                    description
                FROM
                    engines
                WHERE
                    accountid = :accountid
                AND   
                    deleted IS null ';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getEnginesForApi($supplierIds, $domainId)
    {
        $supplierIds = is_array($supplierIds) ? implode(',', $supplierIds) : $supplierIds;
        $supplierIds = empty($supplierIds) ? '0' : $supplierIds;
        $and = $domainId ? ' AND domainid = :domainid' : '';

        $query = 'SELECT
                id,
                name,
                accountid,
                trg,
                src,
                domainid,
                online,
                type,
                created,
                deleted,
                customid,
                ter,
                bleu,
                fmeasure,
                trainingwordcount,
                costperword,
                description
            FROM
                engines
            WHERE
                accountid IN (' . $supplierIds . ')
            AND
                deleted IS NULL
            AND
                src = :src
            AND 
                trg = :trg ' . $and;

        $this->startTransaction();
        $this->query($query);
        $this->bindValue(':trg', $this->getTarget(), PDO::PARAM_STR);
        $this->bindValue(':src', $this->getSource(), PDO::PARAM_STR);
        if ($domainId) {
            $this->bindValue(':domainid', $domainId, PDO::PARAM_INT);
        }
        $result = $this->resultSet();


        // TODO; remove that if we decide to remove the fallback
        if (empty($result)) {
            $query = 'SELECT
                id,
                name,
                accountid,
                trg,
                src,
                domainid,
                online,
                type,
                created,
                deleted,
                customid,
                ter,
                bleu,
                fmeasure,
                trainingwordcount,
                costperword,
                description
            FROM
                engines
            WHERE
                accountid IN (' . $supplierIds . ')
            AND
                deleted IS NULL
            AND
                src = :src
            AND 
                trg = :trg ';


            $this->query($query);
            $this->bindValue(':trg', $this->getTarget(), PDO::PARAM_STR);
            $this->bindValue(':src', $this->getSource(), PDO::PARAM_STR);

            $result = $this->resultSet();
        }

        $this->endTransaction();

        return $result;
    }

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId()) {
            $query = 'UPDATE
							engines
						SET
							name		      = :name,
			                accountid         = :accountid,
							trg	              = :trg,	
							src               = :src,	
							domainid          = :domainid,
							online            = :online,	
							type              = :type,	
			                deleted           = :deleted,
			                customid          = :customid,
			                ter               = :ter,
			                bleu              = :bleu,
			                fmeasure          = :fmeasure,
			                trainingwordcount = :trainingwordcount,
			                costperword       = :costperword,
			                description       = :description
						WHERE
							id			      = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':name', $this->getName(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
            $this->bindValue(':trg', $this->getTarget(), PDO::PARAM_STR);
            $this->bindValue(':src', $this->getSource(), PDO::PARAM_STR);
            $this->bindValue(':customid', $this->getCustomId(), PDO::PARAM_STR);
            $this->bindValue(':ter', $this->getTer(), PDO::PARAM_STR);
            $this->bindValue(':bleu', $this->getBleu(), PDO::PARAM_STR);
            $this->bindValue(':fmeasure', $this->getFmeasure(), PDO::PARAM_STR);
            $this->bindValue(':online', $this->getOnline(), PDO::PARAM_INT);
            $this->bindValue(':type', $this->getType(), PDO::PARAM_INT);
            $this->bindValue(':deleted', $this->getDeleted(), PDO::PARAM_INT);
            $this->bindValue(':trainingwordcount', $this->getTrainingWordCount(), PDO::PARAM_STR);
            $this->bindValue(':costperword', $this->getCostPerWord(), PDO::PARAM_STR);
            $this->bindValue(':description', $this->getDescription(), PDO::PARAM_STR);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    public function delete()
    {
        if (!empty($this->getId())) {
            $query = 'DELETE FROM
						engines
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
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
            $this->setName($details['name'] ? $details['name'] : null);
            $this->setAccountId($details['accountid'] ? $details['accountid'] : null);
            $this->setTarget($details['trg'] ? $details['trg'] : null);
            $this->setSource($details['src'] ? $details['src'] : null);
            $this->setDomainId($details['domainid'] ? $details['domainid'] : null);
            $this->setOnline($details['online']);
            $this->setType($details['type'] ? $details['type'] : null);
            $this->setCreated($details['created'] ? $details['created'] : null);
            $this->setDeleted($details['deleted'] ? $details['deleted'] : null);
            $this->setCustomId($details['customid'] ? $details['customid'] : null);
            $this->setTer($details['ter'] ? $details['ter'] : null);
            $this->setBleu($details['bleu'] ? $details['bleu'] : null);
            $this->setFmeasure($details['fmeasure'] ? $details['fmeasure'] : null);
            $this->setTrainingWordCount($details['trainingwordcount'] ? $details['trainingwordcount'] : null);
            $this->setCostPerWord($details['costperword'] ? $details['costperword'] : null);
            $this->setDescription($details['description'] ? $details['description'] : null);

            return true;
        }

        return false;
    }

    public function isDomainAssigned()
    {
        if (!empty($this->getDomainId())) {
            $query = 'SELECT
                        id
					FROM
						engines
					WHERE 
					    domainid  = :domainid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':domainid', $this->getDomainId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            if ($result) {
                return true;
            }
        }

        return false;
    }

    public function generateEngineCopyName($name)
    {
        // Get all engines name
        $engines = $this->getAll();
        // Check if the name looks like its been already copied (contains at the end - and number)
        $baseName = preg_match('/-[0-9]*$/', $name) ? preg_replace('/-[0-9]*$/', '', $name) : $name;

        $i = 1;
        do {
            $unique = true;
            foreach ($engines as $engine) {
                $engineName = $engine['name'];

                if ($name == $engineName) {
                    $name = $baseName . '-' . $i++;
                    $unique = false;
                }
            }
        } while ($unique && ($name != $engineName));

        return $name;
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
        if (is_numeric($id)) {
            $this->id = $id;
        }
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param null $accountId
     */
    public function setAccountId($accountId)
    {
        if (is_numeric($accountId)) {
            $this->accountId = $accountId;
        }
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $type
     */
    public function setType($type)
    {
        if (is_numeric($type)) {
            $this->type = $type;
        }
    }

    /**
     * @return null
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param null $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return null
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param null $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return null
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * @param null $online
     */
    public function setOnline($online)
    {
        if (is_numeric($online)) {
            $this->online = $online;
        }
    }

    /**
     * @return null
     */
    public function getCustomId()
    {
        return $this->customId;
    }

    /**
     * @param null $customId
     */
    public function setCustomId($customId)
    {
        $this->customId = $customId;
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
    public function getTer()
    {
        return $this->ter;
    }

    /**
     * @param null $ter
     */
    public function setTer($ter)
    {
        $this->ter = $ter;
    }

    /**
     * @return null
     */
    public function getBleu()
    {
        return $this->bleu;
    }

    /**
     * @param null $bleu
     */
    public function setBleu($bleu)
    {
        $this->bleu = $bleu;
    }

    /**
     * @return null
     */
    public function getFmeasure()
    {
        return $this->fmeasure;
    }

    /**
     * @param null $fmeasure
     */
    public function setFmeasure($fmeasure)
    {
        $this->fmeasure = $fmeasure;
    }

    /**
     * @return null
     */
    public function getTrainingWordCount()
    {
        return $this->trainingWordCount;
    }

    /**
     * @param null $trainingWordCount
     */
    public function setTrainingWordCount($trainingWordCount)
    {
        if (is_numeric($trainingWordCount)) {
            $this->trainingWordCount = $trainingWordCount;
        }
    }

    /**
     * @return null
     */
    public function getCostPerWord()
    {
        return $this->costPerWord;
    }

    /**
     * @param null $costPerWord
     */
    public function setCostPerWord($costPerWord)
    {
        if (is_numeric($costPerWord)) {
            $this->costPerWord = $costPerWord;
        }
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
