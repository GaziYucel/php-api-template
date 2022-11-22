<?php
/**
 * This class contains the router logic.
 */

namespace REPA\App;

use ReflectionMethod;

class Router
{
    function __construct()
    {
        // if path not found in query string, response with syntax
        if (empty($_REQUEST[PATH_KEY])) $this->response(['Syntax: ' . SYNTAX], 200);

        // check if authenticated
        $auth = new Auth();
        if (!$auth->isAuthenticated()) $this->response(['Unauthorised'], 401);

        // get value of path in query string
        $query = explode('/', trim($_REQUEST[PATH_KEY], "/"));

        // get endpoint name
        $endpointName = ucfirst(strtolower(array_shift($query)));

        // is endpoint allowed
        if (stristr(' ' . EXCLUDED_ENDPOINTS . ' ', ' ' . $endpointName . ' '))
            $this->response(["Endpoint [$endpointName] not allowed"], 405);

        // does endpoint file exist
        if (!file_exists(ENDPOINTS_DIR . '/' . $endpointName . '.php'))
            $this->response(["Endpoint [$endpointName] not found"], 404);

        // require endpoint class file
        require_once(ENDPOINTS_DIR . '/' . $endpointName . ".php");

        // instantiate object
        $endpointObject = new $endpointName();

        // execute action
        $this->executeAction($endpointObject);
    }

    /**
     * Execute action
     * @param object $endpoint
     * @return void
     */
    private function executeAction(object $endpoint): void
    {
        // action empty
        if (empty($endpoint->action)) {
            switch ($endpoint->method) {
                default:
                case 'GET':
                    $this->response($endpoint->get(), $endpoint->status);
                    break;
                case 'POST':
                    $this->response($endpoint->post(), $endpoint->status);
                    break;
                case 'PUT':
                    $this->response($endpoint->put(), $endpoint->status);
                    break;
                case 'DELETE':
                    $this->response($endpoint->delete(), $endpoint->status);
                    break;
            }
        }

        // is action allowed
        if (stristr(' ' . EXCLUDED_ACTIONS . ' ', ' ' . $endpoint->action . ' '))
            $this->response(["Action [$endpoint->action] not allowed"], 405);

        // execute action if exists
        if(method_exists($endpoint, $endpoint->action)){
            $reflection = new ReflectionMethod($endpoint, $endpoint->action);
            if ($reflection->isPublic()) {
                $this->response($endpoint->{$endpoint->action}(), $endpoint->status);
            }
        }

        // nothing found
        $this->response(["Action [$endpoint->action] not found!"], 404);
    }

    /**
     *  The method which sends the response to the client
     * @param array|string $data
     * @param int $status
     * @return void
     */
    private function response(array|string $data, int $status): void
    {
        $httpStatusCodes = HTTP_STATUS_CODES[200];
        if (key_exists($status, HTTP_STATUS_CODES))
            $httpStatusCodes = HTTP_STATUS_CODES[$status];

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: " . implode(",", METHODS_ALLOWED));
        header("HTTP/1.1 $status $httpStatusCodes");
        header("Content-Type: application/json");

        if (!is_array($data)) $data = [$data];

        echo json_encode($data);

        exit;
    }
}
