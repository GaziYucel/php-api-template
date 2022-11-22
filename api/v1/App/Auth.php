<?php
/**
 * This class needs more business logic.
 * At this moment, only true is returned.
 * Most basic usage would be:
 * - add a token at savedToken
 * - check this token with the token send in the header with every request
 * - the name for the token is given in Config.php, e.g. AuthenticationToken
 *
 * Schema:
 *     {
 *       "id": { "type": "int", "primaryKey": true, "label": "Id", "enum": [] },
 *       "token": { "type": "string", "primaryKey": false, "label": "Autherisation Token", "enum": [] }
 *     }
 */

namespace REPA\App;

class Auth
{
    /**
     * Dao object
     * @var object
     */
    protected object $dao;

    /**
     *  The token
     * @value Token: 1234567890
     * @var string
     */
    public string $token = '';

    /**
     *  This is the saved token; this should come from some other place like database e.d.
     * @var string
     */
    private string $savedToken = '';

    function __construct()
    {
        // get token from header
        $header = getallheaders();
        if (!empty($header[TOKEN_KEY]))
            $this->token = $header[TOKEN_KEY];

        // initialise dao object
        $this->dao = new Dao('auth');

        // get row for this token
        $row = $this->dao->getRowByKey('token', $this->token);
        if(count($row) > 0) $this->savedToken = $row['token'];
    }

    /**
     *  This method checks if the request is authenticated
     * @return bool
     */
    function isAuthenticated(): bool
    {
        if(!empty($this->token) && $this->token == $this->savedToken)
            return true;

        return false;
    }
}
