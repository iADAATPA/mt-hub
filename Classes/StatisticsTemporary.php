<?php

/**
 * Class StatisticsTemporary
 */
class StatisticsTemporary extends Statistics
{
    const TABLE_NAME = "statisticstemporary";

    /**
     * StatisticsTemporary constructor.
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
     * Delete temporary statistics records
     *
     * @param array $idList
     * @return bool|null
     */
    public function deleteRecords($idList)
    {
        $ids = is_array($idList) ? implode(",", $idList) : $idList;

        if (!empty($ids) && !empty($this->getTable())) {
            $query = 'DELETE FROM
						' . $this->getTable() . '
					WHERE 
					    id IN (' . $ids . ')';

            $this->startTransaction();
            $this->query($query);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }
}
