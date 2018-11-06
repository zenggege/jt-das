<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// Note: Only the widely used HTTP status codes are documented
// Informational

define('HTTP_CONTINUE', 100);
define('HTTP_SWITCHING_PROTOCOLS', 101);
define('HTTP_PROCESSING', 102); // RFC2518
// Success
/**
 * The request has succeeded
 */
define('HTTP_OK', 200); 
/**
 * The server successfully created a new resource
 */

define('HTTP_CREATED', 201); 
define('HTTP_ACCEPTED', 202); 
define('HTTP_NON_AUTHORITATIVE_INFORMATION', 203); 
/**
 * The server successfully processed the request, though no content is returned
 */

define('HTTP_NO_CONTENT', 204); 
define('HTTP_RESET_CONTENT', 205); 
define('HTTP_PARTIAL_CONTENT', 206); 
define('HTTP_ALREADY_REPORTED', 208);       // RFC5842
define('HTTP_MULTI_STATUS', 207);          // RFC4918
define('HTTP_IM_USED', 226);               // RFC3229

// Redirection
define('HTTP_MULTIPLE_CHOICES',300);
define('HTTP_MOVED_PERMANENTLY',301);
define('HTTP_FOUND',302);
define('HTTP_SEE_OTHER',303);
/**
 * The resource has not been modified since the last request
 */
define('HTTP_NOT_MODIFIED',304);
define('HTTP_USE_PROXY',305);
define('HTTP_RESERVED',306);
define('HTTP_TEMPORARY_REDIRECT',307);
define('HTTP_PERMANENTLY_REDIRECT',308);  // RFC7238
// Client Error
/**
 * The request cannot be fulfilled due to multiple errors
 */
define('HTTP_BAD_REQUEST',400);
/**
 * The user is unauthorized to access the requested resource
 */
define('HTTP_UNAUTHORIZED',401);
define('HTTP_PAYMENT_REQUIRED',402);
/**
 * The requested resource is unavailable at this present time
 */
define('HTTP_FORBIDDEN',403);
/**
 * The requested resource could not be found
 *
 * Note: This is sometimes used to mask if there was an UNAUTHORIZED (401) or
 * FORBIDDEN (403) error, for security reasons
 */
define('HTTP_NOT_FOUND',404);
/**
 * The request method is not supported by the following resource
 */
define('HTTP_METHOD_NOT_ALLOWED',405);
/**
 * The request was not acceptable
 */
define('HTTP_NOT_ACCEPTABLE',406);
define('HTTP_PROXY_AUTHENTICATION_REQUIRED',407);
define('HTTP_REQUEST_TIMEOUT',408);
/**
 * The request could not be completed due to a conflict with the current state
 * of the resource
 */
define('HTTP_CONFLICT',409);
define('HTTP_GONE',410);
define('HTTP_LENGTH_REQUIRED',411);
define('HTTP_PRECONDITION_FAILED',412);
define('HTTP_REQUEST_ENTITY_TOO_LARGE',413);
define('HTTP_REQUEST_URI_TOO_LONG',414);
define('HTTP_UNSUPPORTED_MEDIA_TYPE',415);
define('HTTP_REQUESTED_RANGE_NOT_SATISFIABLE',416);
define('HTTP_EXPECTATION_FAILED',417);
define('HTTP_I_AM_A_TEAPOT',418);                                               // RFC2324
define('HTTP_UNPROCESSABLE_ENTITY',422);                                        // RFC4918
define('HTTP_LOCKED',423);                                                      // RFC4918
define('HTTP_FAILED_DEPENDENCY',424);                                           // RFC4918
define('HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL',425);   // RFC2817
define('HTTP_UPGRADE_REQUIRED',426);                                            // RFC2817
define('HTTP_PRECONDITION_REQUIRED',428);                                       // RFC6585
define('HTTP_TOO_MANY_REQUESTS',429);                                           // RFC6585
define('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE',431);                             // RFC6585
// Server Error
/**
 * The server encountered an unexpected error
 *
 * Note: This is a generic error message when no specific message
 * is suitable
 */
define('HTTP_INTERNAL_SERVER_ERROR',500);
/**
 * The server does not recognise the request method
 */
define('HTTP_NOT_IMPLEMENTED',501);
define('HTTP_BAD_GATEWAY',502);
define('HTTP_SERVICE_UNAVAILABLE',503);
define('HTTP_GATEWAY_TIMEOUT',504);
define('HTTP_VERSION_NOT_SUPPORTED',505);
define('HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL',506);                        // RFC2295
define('HTTP_INSUFFICIENT_STORAGE',507);                                        // RFC4918
define('HTTP_LOOP_DETECTED',508);                                               // RFC5842
define('HTTP_NOT_EXTENDED',510);                                                // RFC2774
define('HTTP_NETWORK_AUTHENTICATION_REQUIRED',511);


define('SYS_TIME', time());
define('SYS_DATE',date('Y-m-d H:i:s'));

define('RSA_PRIVATE_KEY', '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEA0gbPkfcMvbuvmwFq4ki5WYssD9hpQIdeRpokvpX2VFQUKs/2
ay/ZdgFz2qzmLOj324vI9g4QZlO+n2RtlDzZVcJySXiUQOiQRl+UX2BRjuxtYP7t
+rA73d+qqfxwsCp6toRhSKZjHmQte77nnwA/b+OBIXGZbHeRMgmBaYOxqbO8K+rQ
Pzvq4IbUcIygm68eZXU7GWBSlM8vAmlR2aPGYzfx74rd/VGuF4REbRUBGL0oP+k4
dHVEBOEGzlMojZI3Pa7u/g6SUqZd6an5TM4WcjI2DkozI8bK1Vr+no4ow3wlm6Ef
NMlCiI7p8z4u6ZYlI2LoxzVrigOP4X5kvAV66QIDAQABAoIBAEoJypybKautAT+0
ZTh2CJkPXa4MTTZJQivDZRamiNq7dQyOmUC58oAODQhWkSHd0ppbkbFjzdY0yWsp
HRP3y2nXDTDnR+QF1+5m/UgODVJD+F8MH1qTS9dwZJpd+l+v6ehzjFPvC9wH1pzb
9IzxqmtIGRpwEHTTRaUNAsjueu/mX/fGt3kpnoZOoDet9qosbQccDpEzet3bHWu/
qpsf/6+EmvxT7dIX+xAcL+klasThatXTzrwBVVp8fe53i0vYNIBB6uhiBKeGISuB
/iPGizWolACn4hUru33k7NNoCY49a6D9tPX442snrlkToW7HEuXYTP1SHq7N/z8P
Hd3yhS0CgYEA7KRsHr5FXhiPaXv4yCm5WwTaPqxwqE7I+XGHh3Hbpgx7gZb/Q/7G
YGyy+Y1rfTEUafglKbO564UGf5Q5KWi9gIjIsG4Eb1WMQV+tpmLx8C0aDTob3RmT
66GM4vnfK0lz6eA0Vmp9z7RZfxW8Xc/+7+OVQhG5yLu283C3GekIT9cCgYEA4zUG
HsuO3seqZnyGp89ZHIzAHD8sAq8BCuJY30ixgFwB4HCrUCx3GfDmokaCuOaxbDaP
sc8gAsqVlwGpZWSvH65eP2xy58RN3L1dlYXZGjkKcfh+fIuJN6GiLPhSss+kjUKb
bIvHSItI5FX3gvQcF7sY6fZqUQ1uIUbVZiM+Mz8CgYEAyucccavRjKHgbaDHqtDj
xmA9xVlT62xKNF+cxozgudqgF3hh/Wo5rDnnp8QTcy+fAlGrg7s/4eqYrNFpxdCy
E8C021op4VBnxzIDkdPrAHWbjdXSSF0DERne1EtNiC8d/V2pYqNCptJLnoUDkyih
vgzUUOcj9jLF5qwxwzylTNcCgYEAzwJdnCs89a+Xip8ElNpvR3raAiJwd9V5LrCB
5pY120x5DUO6YmbQ8RqzF8EEk3Dk0EJ587hSYxiu6JHEBDSS9luJVWH64z3Q70C9
hmEYKDM2WBbfL5x2nfAvcaeiuXZEZ8v7Dg/gtzDobdoBlBfTjK+UCH7R6R1CbSE7
hz6vCkMCgYAvJj5RNgWnoHaColVu0UE8wTPFZFH6ZmqHMNJQ+RGldYb6vWUuyEB3
W8IDXGKv0sd5LVsqAHtETxbmiJDONwQAotXQr/O7WpdcN9AK1bNKJmYuE/4Rvy0C
o2cq/SACPf7mN+i91Eip8PeNboRx/OBYqratCM2BRUnjF49YaHVSnw==
-----END RSA PRIVATE KEY-----');

define('RSA_PUBLIC_KEY', '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0gbPkfcMvbuvmwFq4ki5
WYssD9hpQIdeRpokvpX2VFQUKs/2ay/ZdgFz2qzmLOj324vI9g4QZlO+n2RtlDzZ
VcJySXiUQOiQRl+UX2BRjuxtYP7t+rA73d+qqfxwsCp6toRhSKZjHmQte77nnwA/
b+OBIXGZbHeRMgmBaYOxqbO8K+rQPzvq4IbUcIygm68eZXU7GWBSlM8vAmlR2aPG
Yzfx74rd/VGuF4REbRUBGL0oP+k4dHVEBOEGzlMojZI3Pa7u/g6SUqZd6an5TM4W
cjI2DkozI8bK1Vr+no4ow3wlm6EfNMlCiI7p8z4u6ZYlI2LoxzVrigOP4X5kvAV6
6QIDAQAB
-----END PUBLIC KEY-----');

define('DEFAULT_PAGE_SIZE', 100);