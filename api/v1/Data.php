<?php
/**
 * This class is an example of how to implement an endpoint.
 */
require_once('App/Endpoint.php');

class Data extends Endpoint
{
    protected $dataPath = 'data/data.json';
    protected $schemaPath = 'data/schema.json';

    protected $data = [];
    protected $schema = [];
    protected $schemaKey = 'id';
    protected $requiredColumns = [];

    function __construct()
    {
        parent::__construct();

        $this->getSchemaFromFile();
        $this->getDataFromFile();
    }

    /**
     * @desc Triggered if no action is given and method is GET
     * @return array
     */
    public function get(): array
    {
        // no identifiers given, return all
        if (empty($this->identifiers))
            return $this->data;

        // identifiers given, return rows for ids
        $filtered = $this->getDataByIds($this->identifiers);
        if (empty($filtered))
            $this->status = 404;

        return $filtered;
    }

    /**
     * @desc Triggered if no action is given and method is POST
     * @return array
     */
    public function post(): array
    {
        return $this->put();
    }

    /**
     * @desc Triggered if no action is given and method is PUT
     * @return array
     */
    public function put(): array
    {
        // required columns check
        if (count($this->getMissingRequiredColumns($this->request)) > 0)
            return $this->request;

        // make row schema conform
        $row = $this->migrateRowToSchema($this->request);

        // id not given, create new row
        if (empty($row[$this->schemaKey])) {
            $row[$this->schemaKey] = $this->getNewId();
            $this->data[] = $row;
        }

        // id given, update row
        if (!empty($row[$this->schemaKey])) {
            // if id not found, return not found
            if (empty($this->getDataByIds([$row[$this->schemaKey]]))) {
                $this->status = 404;
                return $row;
            }

            // id found, update
            foreach ($this->data as $dIndex => $dRow)
                if ($dRow[$this->schemaKey] == $row[$this->schemaKey])
                    foreach ($row as $key => $value)
                        $this->data[$dIndex][$key] = $value;
        }

        $this->saveDataToFile();

        return $row;
    }

    /**
     * @desc Triggered if no action is given and method is DELETE
     * @return array
     */
    public function delete(): array
    {
        // if identifiers given, delete and return results
        if (!empty($this->identifiers)) {
            $deletedIds = [];
            $result = [];

            // find matching rows and skip row
            foreach ($this->data as $row) {
                $rowMatch = false;
                foreach ($this->identifiers as $id) {
                    if ($row[$this->schemaKey] == $id) {
                        $rowMatch = true;
                        $deletedIds[] = $id;
                    }
                }

                // row not found, include row
                if (!$rowMatch) $result[] = $row;
            }

            $this->data = $result;

            $this->saveDataToFile();

            return $deletedIds;
        }

        return $this->identifiers;
    }

    /**
     * @desc Get schema from file
     * @return void
     */
    protected function getSchemaFromFile(): void
    {
        $this->schema = json_decode(
            file_get_contents($this->schemaPath), true);

        foreach ($this->schema as $key => $value) {
            // set required columns
            if ($value['required'] === true) $this->requiredColumns[] = $key;

            // set schema key
            if ($value['key'] === true) $this->schemaKey = $key;
        }
    }

    /**
     * @desc Get data from file
     * @return void
     */
    protected function getDataFromFile(): void
    {
        $this->data = json_decode(
            file_get_contents($this->dataPath), true);

        if (empty($this->data)) $this->data = [];

        $result = [];

        foreach ($this->data as $row)
            $result[] = $this->migrateRowToSchema($row);

        $this->data = $result;
    }

    /**
     * @desc Save data to file
     * @return void
     */
    protected function saveDataToFile(): void
    {
        $this->data = array_unique($this->data, SORT_REGULAR);

        sort($this->data);

        file_put_contents($this->dataPath,
            json_encode($this->data, JSON_PRETTY_PRINT));
    }

    /**
     * @desc Get only matching rows
     * @param $ids
     * @return array
     */
    protected function getDataByIds($ids): array
    {
        $result = [];

        foreach ($this->data as $row) {
            foreach ($ids as $id) {
                if ($row[$this->schemaKey] == $id) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    /**
     * @desc Get missing required columns
     * @param $row
     * @return array
     */
    protected function getMissingRequiredColumns($row): array
    {
        $keys = [];

        foreach ($this->requiredColumns as $key) {
            if (!array_key_exists($key, $row)) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * @desc Get id for new row (max id + 1)
     * @return int
     */
    protected function getNewId(): int
    {
        $dataIds = [];

        foreach ($this->data as $value) {
            $dataIds[] = $value[$this->schemaKey];
        }

        arsort($dataIds);

        $lastId = array_shift($dataIds);

        return $lastId + 1;
    }

    /**
     * @desc Migrate row to schema conform; ignore columns not in the schema
     * @param $row
     * @return array
     */
    protected function migrateRowToSchema($row): array
    {
        $result = [];

        foreach ($this->schema as $key => $value)
            $result[$key] = $row[$key];

        return $result;
    }
}
