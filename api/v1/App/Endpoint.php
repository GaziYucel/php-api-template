<?php
/**
 * This class contains the endpoint logic.
 */

namespace REPA\App;

class Endpoint
{
    /**
     *  HTTP Status Code
     * @var int
     */
    public int $status = 200;

    /**
     *  The HTTP method
     * @value GET, POST, PUT or DELETE
     * @var string
     */
    public string $method = 'GET';

    /**
     * The request
     * @value e.g. /demo/process/1 ; form data ; file
     * @var array
     */
    public array $request = [];

    /**
     *  The action requested
     * $value eg: action=action-name
     * @var string
     */
    public string $action = '';

    /**
     *  The identifiers requested
     * $value eg: ids=1,2,3
     * @var int[]
     */
    public array $identifiers = [];

    /**
     * Dao object
     * @var object
     */
    protected object $dao;

    function __construct()
    {
        // get value of path in query string
        $query = explode('/', trim($_REQUEST[PATH_KEY], "/"));

        // get rid of the first part, e.g. endpoint name
        array_shift($query);

        // set action if any
        if(!empty($query[0]) && !is_numeric($query[0]))
            $this->action = array_shift($query);

        // set identifiers
        $this->identifiers = array_unique($query);

        // set method
        $this->method = $_SERVER['REQUEST_METHOD'];

        // set request
        switch ($this->method) {
            case 'POST':
                $this->request = $_POST;
                break;
            case 'PUT':
                parse_str(file_get_contents("php://input"),
                    $this->request);
                break;
            default:
            case 'GET':
            case 'DELETE':
                $this->request = $_GET;
                break;
        }

        $this->dao = new Dao(strtolower(get_class($this)));
    }
    
    /**
     *  Triggered if no action is given and method is GET
     * @return array
     */
    public function get(): array
    {
        return ["::get"];
    }

    /**
     *  Triggered if no action is given and method is POST
     * @return array
     */
    public function post(): array
    {
        return ["::post"];
    }

    /**
     *  Triggered if no action is given and method is PUT
     * @return array
     */
    public function put(): array
    {
        return ["::put"];
    }

    /**
     *  Triggered if no action is given and method is DELETE
     * @return array
     */
    public function delete(): array
    {
        return ["::delete"];
    }
}
