<?php
/**
 * This class contains the endpoint logic.
 */
require_once('Api.php');

class Endpoint extends Api
{
    function __construct()
    {
        // execute parent __construct
        parent::__construct();

        // set request
        switch ($this->method) {
            case 'POST':
                $this->request = $this->stripTags($_POST);
                break;
            case 'PUT':
                parse_str(file_get_contents("php://input"),
                    $this->request);
                break;
            default:
            case 'GET':
            case 'DELETE':
                $this->request = $this->stripTags($_GET);
                break;
        }
    }

    /**
     * @desc Triggered if no action is given and method is GET
     * @return array
     */
    public function get(): array
    {
        return ["::get"];
    }

    /**
     * @desc Triggered if no action is given and method is POST
     * @return array
     */
    public function post(): array
    {
        return ["::post"];
    }

    /**
     * @desc Triggered if no action is given and method is PUT
     * @return array
     */
    public function put(): array
    {
        return ["::put"];
    }

    /**
     * @desc Triggered if no action is given and method is DELETE
     * @return array
     */
    public function delete(): array
    {
        return ["::delete"];
    }

    /**
     * @desc Execute action; allowed methods: is public, not a magic method, method exists
     * @return array
     */
    public function executeAction(): array
    {
        // return default if no action given
        if (empty($this->action)) {
            switch ($this->method) {
                default:
                case 'GET':
                    return $this->get();
                case 'POST':
                    return $this->post();
                case 'PUT':
                    return $this->put();
                case 'DELETE':
                    return $this->delete();
            }
        }

        // check if action allowed
        if (key_exists(
            strtolower($this->action),
            array_change_key_case(EXCLUDED_ACTIONS, CASE_LOWER))) {
            $this->status = 404;
            return ["Action [$this->action] not found"];
        }

        // execute action
        try {
            $reflection = new ReflectionMethod($this, $this->action);
            if ($reflection->isPublic() && method_exists($this, $this->action))
                return $this->{$this->action}();
        } catch (ReflectionException|Exception $ex) {
        }

        // nothing found
        $this->status = 404;
        return ["Action [$this->action] not found"];
    }

    /**
     * @desc Strip tags in string and array
     * @param $data
     * @return array|string
     */
    protected function stripTags($data)
    {
        $result = array();

        if (is_array($data))
            foreach ($data as $key => $value)
                $result[$key] = $this->stripTags($value);

        if(!is_array($data))
            $result = trim(strip_tags($data));

        return $result;
    }
}
