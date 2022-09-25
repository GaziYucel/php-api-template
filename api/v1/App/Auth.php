<?php
/**
 * This class needs more business logic.
 * At this moment, only true is returned.
 */
require_once('Config.php');

class Auth
{
    /**
     * @desc The ip of the requester
     * @var string
     */
    protected $ip;

    /**
     * @desc This is the saved ip; this should come from some other place like database e.d.
     * @var string
     */
    protected $savedIp = '';

    /**
     * @desc The token
     * @value Token: 1234567890
     * @var string
     */
    protected $token;

    /**
     * @desc This is the saved token; this should come from some other place like database e.d.
     * @var string
     */
    protected $savedToken = '';

    function __construct()
    {
        // set request ip
        $this->ip = $_SERVER['REMOTE_ADDR'];

        // get token from header
        $header = getallheaders();
        if (!empty($header[TOKEN_KEY]))
            $this->token = $header[TOKEN_KEY];
    }

    /**
     * @desc This method checks if the request is authenticated
     * @return bool
     */
    function isAuthenticated(): bool
    {
        return true;
    }
}
