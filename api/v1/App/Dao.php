<?php
/**
 * Data Access Object
 */

namespace REPA\App;

class Dao
{
    /**
     * Data source object
     * @var object
     */
    private object $ds;

    function __construct(string $model)
    {
        switch(DATA_SOURCE_TYPE){
            default:
                $this->ds = new FsDs($model);
                break;
        }
    }

    /**
     * Get all rows
     * @return array
     */
    public function getRows(): array
    {
        return $this->ds->getRows();
    }

    /**
     * Get row with key / value pair
     * @param string $key
     * @param string $value
     * @return array
     */
    public function getRowByKey(string $key, string $value): array
    {
        return $this->ds->getRowByKey($key, $value);
    }

    /**
     * Get only matching rows
     * @param array $ids [int]
     * @return array
     */
    public function getRowsByIds(array $ids): array
    {
        return $this->ds->getRowsByIds($ids);
    }

    /**
     * Insert row if no id given, update if id given
     * @param $row
     * @return array
     */
    public function insertOrUpdateRow($row): array
    {
        $primaryKey = $this->ds->getPrimaryKey();

        // id not given, create new row
        if (empty($row[$primaryKey]))
            return $this->ds->insertRow($row);

        // id given and id not found
        if (empty($this->ds->getRowsByIds([$row[$primaryKey]])))
            return $row;

        // id given, update and return row
        return $this->ds->updateRow($row);
    }

    /**
     * Add new row and return updated row
     * @param $row
     * @return array
     */
    public function insertRow($row): array
    {
        return $this->ds->insertRow($row);
    }

    /**
     * Update new row and return updated row
     * @param $row
     * @return array
     */
    public function updateRow($row): array
    {
        return $this->ds->updateRow($row);
    }

    /**
     * Delete rows for given ids and return deleted ids
     * @param $ids
     * @return array
     */
    public function deleteRows($ids): array
    {
        return $this->ds->deleteRows($ids);
    }

    /**
     * Return schema
     * @return array
     */
    public function getSchema(): array
    {
        return $this->ds->getSchema();
    }
}
