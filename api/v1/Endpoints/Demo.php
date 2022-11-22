<?php
/**
 * This class is an example of how to implement an endpoint.
 */

use REPA\App\Endpoint;

class Demo extends Endpoint
{
    /**
     *  Triggered if no action is given and method is GET
     * @return array
     */
    public function get(): array
    {
        // no identifiers given, return all
        if (empty($this->identifiers)) return $this->dao->getRows();

        // identifiers given, return rows for ids
        $filtered = $this->dao->getRowsByIds($this->identifiers);
        if (empty($filtered)) $this->status = 404;
        return $filtered;
    }

    /**
     *  Triggered if no action is given and method is POST
     * @return array
     */
    public function post(): array
    {
        return $this->put();
    }

    /**
     *  Triggered if no action is given and method is PUT
     * @return array
     */
    public function put(): array
    {
        return $this->dao->insertOrUpdateRow($this->request);
    }

    /**
     *  Triggered if no action is given and method is DELETE
     * @return array
     */
    public function delete(): array
    {
        return $this->dao->deleteRows($this->identifiers);
    }

    /**
     * Returns the schema
     * @return array|string[]
     */
    public function getSchema(): array
    {
        return $this->dao->getSchema();
    }
}
