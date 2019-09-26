<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper\Interfaces;

interface ConstantsHttpInterface
{
    // HTTP methods
    public const HTTP_METHOD_DELETE  = 'delete';
    public const HTTP_METHOD_GET     = 'get';
    public const HTTP_METHOD_HEAD    = 'head';
    public const HTTP_METHOD_INVALID = 'invalid';
    public const HTTP_METHOD_OPTIONS = 'options';
    public const HTTP_METHOD_PATCH   = 'patch';
    public const HTTP_METHOD_POST    = 'post';
    public const HTTP_METHOD_PUT     = 'put';

    // HTTP status codes
    public const HTTP_STATUS_MESSAGE_100 = 'Continue';
    public const HTTP_STATUS_MESSAGE_101 = 'Switching protocols';
    public const HTTP_STATUS_MESSAGE_102 = 'Processing';

    public const HTTP_STATUS_MESSAGE_200 = 'Ok';
    public const HTTP_STATUS_MESSAGE_201 = 'Created';
    public const HTTP_STATUS_MESSAGE_202 = 'Accepted';
    public const HTTP_STATUS_MESSAGE_203 = 'Non-authoritative information';
    public const HTTP_STATUS_MESSAGE_204 = 'No content';
    public const HTTP_STATUS_MESSAGE_205 = 'Reset content';
    public const HTTP_STATUS_MESSAGE_206 = 'Partial content';
    public const HTTP_STATUS_MESSAGE_207 = 'Multi-status';

    public const HTTP_STATUS_MESSAGE_300 = 'Multiple choices';
    public const HTTP_STATUS_MESSAGE_301 = 'Moved permanently';
    public const HTTP_STATUS_MESSAGE_302 = 'Found';
    public const HTTP_STATUS_MESSAGE_303 = 'See other';
    public const HTTP_STATUS_MESSAGE_304 = 'Not modified';
    public const HTTP_STATUS_MESSAGE_305 = 'Use proxy';
    public const HTTP_STATUS_MESSAGE_306 = 'Unused';
    public const HTTP_STATUS_MESSAGE_307 = 'Temporary redirect';

    public const HTTP_STATUS_MESSAGE_400 = 'Bad request';
    public const HTTP_STATUS_MESSAGE_401 = 'Authorization required';
    public const HTTP_STATUS_MESSAGE_402 = 'Payment required';
    public const HTTP_STATUS_MESSAGE_403 = 'Forbidden';
    public const HTTP_STATUS_MESSAGE_404 = 'Page not found';
    public const HTTP_STATUS_MESSAGE_405 = 'Method not allowed';
    public const HTTP_STATUS_MESSAGE_406 = 'Not acceptable';
    public const HTTP_STATUS_MESSAGE_407 = 'Proxy authentication required';
    public const HTTP_STATUS_MESSAGE_408 = 'Request time-out';
    public const HTTP_STATUS_MESSAGE_409 = 'Conflict';
    public const HTTP_STATUS_MESSAGE_410 = 'Gone';
    public const HTTP_STATUS_MESSAGE_411 = 'Length required';
    public const HTTP_STATUS_MESSAGE_412 = 'Precondition failed';
    public const HTTP_STATUS_MESSAGE_413 = 'Request entity too large';
    public const HTTP_STATUS_MESSAGE_414 = 'Request-URI too large';
    public const HTTP_STATUS_MESSAGE_415 = 'Unsupported media type';
    public const HTTP_STATUS_MESSAGE_416 = 'Requested range not satisfiable';
    public const HTTP_STATUS_MESSAGE_417 = 'Expectation failed';
    public const HTTP_STATUS_MESSAGE_418 = 'I\'m a teapot';
    public const HTTP_STATUS_MESSAGE_422 = 'Unprocessable entity';
    public const HTTP_STATUS_MESSAGE_423 = 'Locked';
    public const HTTP_STATUS_MESSAGE_424 = 'Failed dependency';
    public const HTTP_STATUS_MESSAGE_425 = 'No code';
    public const HTTP_STATUS_MESSAGE_426 = 'Upgrade required';

    public const HTTP_STATUS_MESSAGE_500 = 'Internal server error';
    public const HTTP_STATUS_MESSAGE_501 = 'Method not implemented';
    public const HTTP_STATUS_MESSAGE_502 = 'Bad gateway';
    public const HTTP_STATUS_MESSAGE_503 = 'Service temporarily unavailable';
    public const HTTP_STATUS_MESSAGE_504 = 'Gateway time-out';
    public const HTTP_STATUS_MESSAGE_505 = 'HTTP version not supported';
    public const HTTP_STATUS_MESSAGE_506 = 'Variant also negotiates';
    public const HTTP_STATUS_MESSAGE_507 = 'Insufficient storage';
    public const HTTP_STATUS_MESSAGE_510 = 'Not extended';

    // HTTP status messages
    public const HTTP_STATUS_CODE_CONTINUE                      = 100;
    public const HTTP_STATUS_CODE_SWITCHING_PROTOCOLS           = 101;
    public const HTTP_STATUS_CODE_PROCESSING                    = 102;

    public const HTTP_STATUS_CODE_OK                            = 200;
    public const HTTP_STATUS_CODE_CREATED                       = 201;
    public const HTTP_STATUS_CODE_ACCEPTED                      = 202;
    public const HTTP_STATUS_CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_STATUS_CODE_NO_CONTENT                    = 204;
    public const HTTP_STATUS_CODE_RESET_CONTENT                 = 205;
    public const HTTP_STATUS_CODE_PARTIAL_CONTENT               = 206;
    public const HTTP_STATUS_CODE_MULTI_STATUS                  = 207;

    public const HTTP_STATUS_CODE_MULTIPLE_CHOICES              = 300;
    public const HTTP_STATUS_CODE_MOVED_PERMANENTLY             = 301;
    public const HTTP_STATUS_CODE_FOUND                         = 302;
    public const HTTP_STATUS_CODE_SEE_OTHER                     = 303;
    public const HTTP_STATUS_CODE_NOT_MODIFIED                  = 304;
    public const HTTP_STATUS_CODE_USE_PROXY                     = 305;
    public const HTTP_STATUS_CODE_UNUSED                        = 306;
    public const HTTP_STATUS_CODE_TEMPORARY_REDIRECT            = 307;

    public const HTTP_STATUS_CODE_BAD_REQUEST                   = 400;
    public const HTTP_STATUS_CODE_ERROR                         = 400;
    public const HTTP_STATUS_CODE_AUTHORIZATION_REQUIRED        = 401;
    public const HTTP_STATUS_CODE_PAYMENT_REQUIRED              = 402;
    public const HTTP_STATUS_CODE_FORBIDDEN                     = 403;
    public const HTTP_STATUS_CODE_PAGE_NOT_FOUND                = 404;
    public const HTTP_STATUS_CODE_METHOD_NOT_ALLOWED            = 405;
    public const HTTP_STATUS_CODE_NOT_ACCEPTABLE                = 406;

    public const HTTP_STATUS_CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_STATUS_CODE_REQUEST_TIMEOUT               = 408;
    public const HTTP_STATUS_CODE_CONFLICT                      = 409;
    public const HTTP_STATUS_CODE_GONE                          = 410;
    public const HTTP_STATUS_CODE_LENGTH_REQUIRED               = 411;
    public const HTTP_STATUS_CODE_PRECONDITION_FAILED           = 412;
    public const HTTP_STATUS_CODE_REQUEST_ENTITY_TOO_LARGE      = 413;
    public const HTTP_STATUS_CODE_REQUEST_URI_TOO_LARGE         = 414;
    public const HTTP_STATUS_CODE_UNSUPPORTED_MEDIA_TYPE        = 415;
    public const HTTP_STATUS_CODE_REQUEST_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_STATUS_CODE_EXPECTATION_FAILED            = 417;
    public const HTTP_STATUS_CODE_IM_A_TEAPOT                   = 418;
    public const HTTP_STATUS_CODE_UNPROCESSABLE_ENTITY          = 422;
    public const HTTP_STATUS_CODE_LOCKED                        = 423;
    public const HTTP_STATUS_CODE_FAILED_DEPENDENCY             = 424;
    public const HTTP_STATUS_CODE_NO_CODE                       = 425;
    public const HTTP_STATUS_CODE_UPGRADE_REQUIRED              = 426;

    public const HTTP_STATUS_CODE_APPLICATION_ERROR               = 500;
    public const HTTP_STATUS_CODE_INTERNAL_SERVER_ERROR           = 500;
    public const HTTP_STATUS_CODE_METHOD_NOT_IMPLEMENTED          = 501;
    public const HTTP_STATUS_CODE_BAD_GATEWAY                     = 502;
    public const HTTP_STATUS_CODE_SERVICE_TEMPORARILY_UNAVAILABLE = 503;
    public const HTTP_STATUS_CODE_Gateway_TIMEOUT                 = 504;
    public const HTTP_STATUS_CODE_HTTP_VERSION_NOT_SUPPORTED      = 505;
    public const HTTP_STATUS_CODE_VARIANT_ALSO_NEGOTIATES         = 506;
    public const HTTP_STATUS_CODE_INSUFFICIENT_STORAGE            = 507;
    public const HTTP_STATUS_CODE_NOT_EXTENDED                    = 510;
}
