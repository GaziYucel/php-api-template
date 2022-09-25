<?php
/**
 * This class contains the router logic.
 *
 * Require this file in index.php and create a new Router instance,
 * e.g. require_once('App/Router.php'); new Router();
 *
 * The endpoints are defined as follows:
 * + put the endpoint files in the same directory as index.php
 * + file name starts with an upper case, e.g. Data.php
 * + class name has the same name as the file, e.g. class Data
 */
require_once('Api.php');
require_once('Auth.php');

class Router extends Api
{
    function __construct()
    {
        // if request not found in URI, response with syntax
        if (empty($_REQUEST[QUERY_KEY]))
            $this->response(['Syntax: ' . SYNTAX]);

        // check if authenticated
        $auth = new Auth();
        if (!$auth->isAuthenticated())
            $this->response(['Unauthorised'], 401);

        // execute parent __construct
        parent::__construct();

        // check if method allowed
        if (!in_array($this->method, $this->methodsAllowed, true))
            $this->response(["Method [$this->method] not found"], 405);

        // check if endpoint in excluded endpoints
        if (key_exists(
            strtolower($this->endpoint),
            array_change_key_case(EXCLUDED_ENDPOINTS))) {
            $this->response(["Endpoint [$this->endpoint] not found"], 404);
        }

        // look for file and execute endpoint
        if (file_exists($this->endpoint . '.php')) {
            require_once($this->endpoint . ".php");
            $endpoint = new $this->endpoint();
            $action = $endpoint->executeAction();
            $this->response($action, $endpoint->status);
        }

        // nothing found, return not found
        $this->response(["Endpoint [$this->endpoint] not found"], 404);
    }

    /**
     * @desc The actual method which sends the response
     * @param $data
     * @param int $status
     * @return void
     */
    protected function response($data, int $status = 200): void
    {
        $httpStatusCodes = HTTP_STATUS_CODES[500];
        if (key_exists($status, HTTP_STATUS_CODES))
            $httpStatusCodes = HTTP_STATUS_CODES[$status];

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: " . implode(",", $this->methodsAllowed));
        header("HTTP/1.1 $status $httpStatusCodes");
        header("Content-Type: application/json");

        if (!is_array($data)) $data = [$data];

        echo json_encode($data);
        exit;
    }
}
