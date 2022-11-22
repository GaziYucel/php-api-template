<?php
/**
 * Configuration of the API App
 */

/**
 * Key for the path in the querystring
 */
const PATH_KEY = 'path';

/**
 *  The syntax of the request url
 */
const SYNTAX = '?path={endpoint}/{action}/{id0}/{id1}/..';

/**
 * Directory name where the endpoints reside
 */
const ENDPOINTS_DIR = 'Endpoints';

/**
 *  The key of the header value, which holds the token
 */
const TOKEN_KEY = 'AuthenticationToken';

/**
 *  The http request methods allowed
 * @var array|string[]
 */
const METHODS_ALLOWED = ["GET ", "POST", "PUT", "DELETE"];

/**
 *  These endpoints are excluded
 */
const EXCLUDED_ENDPOINTS = "index";

/**
 *  Excluded methods
 */
const EXCLUDED_ACTIONS = "executeEndpoint executeAction 
__construct __destruct __call __callStatic __get __set __isset __unset 
__sleep __wakeup __serialize __unserialize __toString __invoke __set_state __clone __debugInfo";

/**
 *  HTTP Status Code Descriptions
 */
const HTTP_STATUS_CODES = [
    200 => 'OK',
    204 => 'No Content',
    301 => 'Permanent Redirect',
    302 => 'Temporary Redirect',
    401 => 'Unauthorised',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    500 => 'Internal Server Error',
    503 => 'Service Unavailable'
];

/**
 * Type of data source
 * FileSystem, MySQL
 */
const DATA_SOURCE_TYPE = 'FileSystem';

/**
 * Directory name where the data files reside for Db\FileSystem
  */
const FILE_SYSTEM_DATA_SOURCE = '../../data';
  