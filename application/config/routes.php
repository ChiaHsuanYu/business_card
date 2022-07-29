<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['sys_msg/(:any)'] = 'sys_msg/index/$1';              //系統通知訊息
$route['users/(:any)'] = 'users/index';                  //用戶管理
$route['password/edit'] = 'password/edit';               //管理員密碼修改
$route['mgt_subject/add'] = 'mgt_subject/add';           //主題新增上傳
$route['mgt_subject/edit'] = 'mgt_subject/edit';         //主題修改
$route['mgt_subject/(:any)'] = 'mgt_subject/index';      //主題維護
$route['mgt_template/add'] = 'mgt_template/add';         //模板元件新增
$route['mgt_template/edit'] = 'mgt_template/edit';       //模板元件修改
$route['mgt_template/(:any)'] = 'mgt_template/index';    //模板維護

$route['line_login/login'] = 'line_login/login';         //第三方登入-Line
$route['google_login/login'] = 'google_login/login';     //第三方登入-Google
$route['facebook_login/login'] = 'facebook_login/login'; //第三方登入-Facebook

$route['mgt_login/logout'] = 'mgt_login/logout';         //登出
$route['(:any)'] = 'mgt_login/index';                    //登入

$route['default_controller'] = 'mgt_login/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
