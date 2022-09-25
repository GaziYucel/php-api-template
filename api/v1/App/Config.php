<?php
/**
 * @desc The syntax of the request URI
 */
const SYNTAX = '?request={endpoint}/{action}/{id0}/{id1}/{id2}';

/**
 * @desc The key in $_REQUEST
 */
const QUERY_KEY = 'request';

/**
 * @desc The key of the header value, which holds the token
 */
const TOKEN_KEY = 'AuthenticationToken';

/**
 * @desc These endpoints are excluded
 */
const EXCLUDED_ENDPOINTS = ["index"];

/**
 * @desc Excluded methods
 */
const EXCLUDED_ACTIONS = ["executeEndpoint", "executeAction",
    "__construct", "__destruct", "__call", "__callStatic", "__get", "__set",
    "__isset", "__unset", "__sleep", "__wakeup", "__serialize", "__unserialize",
    "__toString", "__invoke", "__set_state", "__clone", "__debugInfo"];

/**
 * @desc HTTP Status Code Descriptions
 */
const HTTP_STATUS_CODES = [
    200 => 'OK',
    204 => 'No Content',
    401 => 'Unauthorised',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    500 => 'Internal Server Error'
];
