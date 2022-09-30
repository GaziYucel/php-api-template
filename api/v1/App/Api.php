<?php
/**
 * The main abstract class.
 */
require_once('Config.php');

abstract class Api
{
    /**
     * @desc HTTP Status Code
     * @var int
     */
    public $status = 200;

    /**
     * @desc The request
     * @value eg: /data/process/1 ; form data ; file
     * @var array
     */
    protected $request;

    /**
     * @desc The HTTP method
     * @value GET, POST, PUT or DELETE
     * @var string
     */
    protected $method = 'GET';

    /**
     * @desc The endpoint requested
     * $value eg: /data
     * @var string
     */
    protected $endpoint = '';

    /**
     * @desc The action requested
     * $value eg: /data/process
     * @var string
     */
    protected $action = '';

    /**
     * @desc The identifiers requested
     * $value eg: /data/process/1 /data/process/1/2/3/4
     * @var array
     */
    protected $identifiers = [];

    /**
     * @desc The methods allowed
     */
    protected $methodsAllowed = ["GET", "POST", "PUT", "DELETE"];

    function __construct()
    {
        $requestUri = explode('/', trim($_REQUEST[QUERY_KEY], "/"));

        // set endpoint
        $this->endpoint = array_shift($requestUri);
        $this->endpoint = ucfirst(strtolower($this->endpoint));

        // set action if any
        if (array_key_exists(0, $requestUri) && !is_numeric($requestUri[0]))
            $this->action = array_shift($requestUri);

        // set identifiers
        $this->identifiers = array_unique($requestUri);

        // set method
        $this->method = $_SERVER['REQUEST_METHOD'];
    }
}
