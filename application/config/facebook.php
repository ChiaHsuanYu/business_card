<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  Facebook API Configuration
| -------------------------------------------------------------------
|
| To get an facebook app details you have to create a Facebook app
| at Facebook developers panel (https://developers.facebook.com)
|
|  facebook_app_id               string   Your Facebook App ID.
|  facebook_app_secret           string   Your Facebook App Secret.
|  facebook_login_redirect_url   string   URL to redirect back to after login. (do not include base URL)
|  facebook_logout_redirect_url  string   URL to redirect back to after logout. (do not include base URL)
|  facebook_login_type           string   Set login type. (web, js, canvas)
|  facebook_permissions          array    Your required permissions.
|  facebook_graph_version        string   Specify Facebook Graph version. Eg v3.2
|  facebook_auth_on_load         boolean  Set to TRUE to check for valid access token on every page load.
*/
$config['facebook_app_id']                = '3175810076071778';
$config['facebook_app_secret']            = '5e3e86eae1c10c7398228db3ae57bcbf';
$config['facebook_login_redirect_url']    = 'https://shine.sub.sakawa.com.tw/business_card/facebook_login/login';
// $config['facebook_logout_redirect_url']   = 'http://192.168.88.154/business_card_test/Facebook_login/logout';

$config['facebook_login_type']            = 'web';
$config['facebook_permissions']           = array('email');
$config['facebook_graph_version']         = 'v3.2';
$config['facebook_auth_on_load']          = FALSE;
$config['facebook_access_token_api']          = 'https://graph.facebook.com/v13.0/oauth/access_token';

