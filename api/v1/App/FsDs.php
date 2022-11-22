<?php
/**
 * Data source class for local filesystem (json)
 */

namespace REPA\App;

class FsDs
{
    /**
     * Extensions of the data files
     * @var string
     */
    private string $fileExtensions = 'json';

    /**
     * Directory name where the schemas reside
     * @var string
     */
    private string $schemaDir = 'schema';

    /**
     * Path to the schema file, e.g. data/schema/demo.json
     * @var string
     */
    private string $schemaPath;

    /**
     * Path to the data file, e.g. data/demo.json
     * @var string
     */
    private string $dataPath;

    /**
     * Schema of the data, e.g. [ "id", "name" ]
     * @var array
     */
    private array $schema;

    /**
     * Primary key
     * @var string
     */
    private string $primaryKey;

    /**
     * All rows
     * @var array
     */
    private array $rows = [];

    function __construct(string $model)
    {
        $this->dataPath =
            FILE_SYSTEM_DATA_SOURCE . '/' .
            strtolower($model) . '.' . $this->fileExtensions;

        $this->schemaPath =
            FILE_SYSTEM_DATA_SOURCE . '/' .
            $this->schemaDir . '/' .
            strtolower($model) . '.' . $this->fileExtensions;

        $this->checkRequirements();

        $this->schema = $this->getSchemaFromFile();

        $this->rows = $this->getAllRowsFromFile();
    }

    /**
     * Get all rows
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Get row with key / value pair
     * @param string $key
     * @param string $value
     * @return array
     */
    public function getRowByKey(string $key, string $value): array
    {
        $filteredRow = [];

        foreach ($this->rows as $row) {
            if ($row[$key] == $value) {
                foreach($row as $key => $value){
                    $filteredRow[$key] = $value;
                }
            }
        }

        return $filteredRow;
    }

    /**
     * Get only matching rows
     * @param array $ids [int]
     * @return array
     */
    public function getRowsByIds(array $ids): array
    {
        $filteredRows = [];
        foreach ($this->rows as $row) {
            foreach ($ids as $id) {
                if ($row[$this->primaryKey] == $id) {
                    $filteredRows[] = $row;
                }
            }
        }
        return $filteredRows;
    }

    /**
     * Add new row and return updated row
     * @param $row
     * @return array
     */
    public function insertRow($row): array
    {
        // make row schema conform
        $row = $this->migrateRowToSchema($row);
        $row[$this->primaryKey] = $this->getNewId();
        $this->rows[] = $row;
        $this->saveRowsToFile($this->rows);
        return $row;
    }

    /**
     * Update new row and return updated row
     * @param $row
     * @return array
     */
    public function updateRow($row): array
    {
        // make row schema conform
        $row = $this->migrateRowToSchema($row);

        foreach ($this->rows as $dIndex => $dRow) {

            if ($dRow[$this->primaryKey] == $row[$this->primaryKey]) {

                foreach ($row as $key => $value) {

                    $this->rows[$dIndex][$key] = $value;
                }
            }
        }

        $this->saveRowsToFile($this->rows);

        return $row;
    }

    /**
     * Delete rows for given ids and return deleted ids
     * @param $ids
     * @return array
     */
    public function deleteRows($ids): array
    {
        if (!empty($ids)) {

            $deletedIds = [];

            $result = [];

            // find matching rows and skip row
            foreach ($this->rows as $row) {

                $rowMatch = false;

                foreach ($ids as $id) {

                    if ($row[$this->primaryKey] == $id) {
                        $rowMatch = true;
                        $deletedIds[] = $id;
                    }
                }

                // row not found, include row
                if (!$rowMatch) $result[] = $row;
            }

            $this->rows = $result;
            $this->saveRowsToFile($this->rows);

            return $deletedIds;
        }

        return $ids;
    }

    /**
     * Return schema
     * @return array
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Return primary key
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get id for new row (max id + 1)
     * @return int
     */
    private function getNewId(): int
    {
        $ids = [];
        foreach ($this->rows as $value) {
            $ids[] = $value[$this->primaryKey];
        }
        arsort($ids);
        $lastId = array_shift($ids);
        return $lastId + 1;
    }

    /**
     * Migrate row to schema conform; ignore columns not in the schema
     * @param $row
     * @return array
     */
    private function migrateRowToSchema($row): array
    {
        $migratedRow = [];
        foreach ($this->schema as $key) {
            $migratedRow[$key] = $row[$key];
            if ($key === $this->primaryKey)
                $migratedRow[$key] = (int)$migratedRow[$key];
        }
        return $migratedRow;
    }

    /**
     * Get all rows from file
     * @return array
     */
    private function getAllRowsFromFile(): array
    {
        $rows = json_decode(file_get_contents($this->dataPath), true);

        if (empty($rows)) $rows = [];

        return $rows;
    }

    /**
     * Save rows to file
     * @param $rows
     * @return void
     */
    private function saveRowsToFile($rows): void
    {
        $rows = array_unique($rows, SORT_REGULAR);

        sort($rows);

        file_put_contents($this->dataPath,
            json_encode($rows, JSON_PRETTY_PRINT));
    }

    /**
     * Get schema from file
     * @return array
     */
    private function getSchemaFromFile(): array
    {
        $schema = [];

        $schemaFile = json_decode(file_get_contents($this->schemaPath), true);

        if (empty($schemaFile)) return [];

        foreach ($schemaFile as $key => $value) {
            $schema[] = $key;
            if ($value['primaryKey'] === true) $this->primaryKey = $key;
        }

        return $schema;
    }

    /**
     * Check requirements and create if not met
     * @return void
     */
    private function checkRequirements(): void
    {
        // create data directory if not exists
        if (!file_exists(FILE_SYSTEM_DATA_SOURCE))
            mkdir(FILE_SYSTEM_DATA_SOURCE);

        // create schema directory if not exists
        if (!file_exists(FILE_SYSTEM_DATA_SOURCE . '/' . $this->schemaDir))
            mkdir(FILE_SYSTEM_DATA_SOURCE . '/' . $this->schemaDir);

        // create schema file if not exists
        if (!file_exists($this->schemaPath))
            file_put_contents($this->schemaPath, '[]');

        // create data file if not exists
        if (!file_exists($this->dataPath))
            file_put_contents($this->dataPath, '[]');
    }
}
