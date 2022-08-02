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

/* 設定 */
define('TOKENEXPIRED', 28800);                                          //define token expired time 8 hours
define('SMS_NUM', 3);                                                   //簡訊當日發送限制次數
define('SMSEXPIRED', 60);                                               //簡訊發送間隔時間
define('LOGIN_DEVICE_NUM', 1);                                          //登入設備類型限制數量
define('TIME_TO_LIVE', 2592000);                                        //cache存活時間
define('NOTIFY_TIME_TO_LIVE', 60);                                      //notify cache存活時間
define('USER_ORDER', 'personal_superID,personal_name,personal_nickname,personal_avatar,personal_phone,personal_email,personal_social');  //user order預設值
define('COMPANY_ORDER', 'company_name,company_logo,company_industryId,company_position,company_aboutus,company_phone,company_address,company_email,company_gui,company_social');  //company order預設值

/* 前台URI */
define('SOCIAL_ICON_PATH', 'appoint/images/social/');                   //社群圖片路徑
define('SUBJECT_IMAGE_PATH', 'appoint/images/subject/');                //主題圖片路徑
define('SUBJECT_CSS_PATH', 'appoint/css/subject/');                     //主題CSS檔案路徑
define('SYSTEM_AVATAR_PATH', 'appoint/images/system_avatar/');          //系統預設頭像圖片路徑
define('AVATAR_PATH', 'appoint/images/avatar/');                        //個人頭像圖片路徑
define('DEL_AVATAR_PATH', 'appoint/images/del_avatar/');                //未使用到的個人頭像圖片路徑
define('LOGO_PATH', 'appoint/images/logo/');                            //公司LOGO圖片路徑
define('DEL_LOGO_PATH', 'appoint/images/del_logo/');                    //未使用到的公司LOGO圖片路徑
define('VCARD_PATH', 'appoint/vCard/');                                 //vCard路徑
define('SHOWCARD_URL', 'https://shine.sub.sakawa.com.tw/dist/ShowCard/');  //前台個人名片顯示 URL

/* 第三方登入 */
define('TOKEN_URL', 'https://shine.sub.sakawa.com.tw/dist/SocialLoginToken');                                   // 前端取得token頁面並執行登入頁面
define('GOOGLE_CLIENTID', '850954696376-rvhmmuktms09hb9d0v08bss1gskq8j3i.apps.googleusercontent.com'); // define google clientId
define('GOOGLE_CLIENTSECRET', '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');                                  // define google Secret Key
define('GOOGLE_REDIRECT_URI', 'https://shine.sub.sakawa.com.tw/business_card/google_login/login');                                  // define google Secret Key

define('LINE_APP_ID', '1657106852');                                                     // define line appId
define('LINE_APP_SECRET', '7b2ed300faa203ef91bf8321d14cd7be');                           // define line appId
define('LINE_LOGIN_REDIRECT_URL', 'https://shine.sub.sakawa.com.tw/business_card/line_login/login');  // define line redirectURL
define('LINE_ACCESS_TOKEN_API', 'https://api.line.me/oauth2/v2.1/token');                // define line get access_token API
define('LINE_PROFILE_API', 'https://api.line.me/v2/profile');                            // define line get profile API

/* 後台URI */
define('LOGIN_PAGE', 'mgt_login/index');                                //define login page
define('LOGOUT_PAGE', '/mgt_login/logout/');                            //define logout page
