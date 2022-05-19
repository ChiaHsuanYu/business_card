<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/CreatorJwt.php';
class BaseAPIController extends RestController{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->service("Common_service");
        $this->objOfJwt = new CreatorJwt();
        header("X-Frame-Options: DENY");
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // header("Access-Control-Allow-Origin: {https://192.168.88.138}");
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
        // header('Content-Type: application/x-www-form-urlencoded');
        // header('Content-Type: multipart/form-data');
        // header('Content-Type: application/json');
    }

    public function GenToken($user_id, $user_account)
    {
        $tokenData['id'] = $user_id;
        $tokenData['account'] = $user_account;
        $tokenData['timeStamp'] = date('Y-m-d H:i:s');

        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        return array('Token' => $jwtToken);
    }

    public function GenToken_front($user_id, $user_account, $host, $device)
    {
        $tokenData['id'] = $user_id;
        $tokenData['account'] = $user_account;
        $tokenData['host'] = $host;
        $tokenData['device'] = $device;
        $tokenData['timeStamp'] = date('Y-m-d H:i:s');

        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        return array('Token' => $jwtToken);
    }

    public function renewToken($user_id, $user_account)
    {
        $jwtToken = $this->GenToken($user_id, $user_account);

        //update db
        $this->common_service->renewTokenById($user_id, $jwtToken['Token']); //更新 Token, T_CreateDT, T_UpdateDT

        return $jwtToken;
    }

    public function renewToken_front($user_id, $user_account)
    {
        if(isset($this->input->request_headers()['x-forwarded-for'])){
            $host = $this->input->request_headers()['x-forwarded-for'];
        }else{
            $host = "";
        }
        $device = $this->get_device_type();
        $jwtToken = $this->GenToken_front($user_id, $user_account,$host,$device);
        //update db
        $this->common_service->renewTokenById_front($user_id, $jwtToken['Token'],$host,$device); //更新 Token, T_CreateDT, T_UpdateDT

        return $jwtToken;
    }


    public function deleteToken($token)
    {
        return $this->common_service->removeToken($token);
    }

    public function deleteToken_front($token)
    {
        return $this->common_service->removeToken_front($token);
    }

    public function get_device_type(){
        $device = '0';
        $mobile = $this->is_mobile_request();
        if($mobile){
            $device = '1';
        }
        return $device;
    }

    // 檢查登入狀態 Authentication/使用權限 Authorization
    public function checkAA(){
        $bearer = array("Bearer ", "bearer ", "BEARER ");     
        $received_Token = "";
        $headers = $this->input->request_headers();
        $headers = array_change_key_case($headers, CASE_LOWER);
        $headers = $this->input->request_headers('Authorization');
        if (array_key_exists('Authorization', $headers) && $headers['Authorization'] != '') {
            $received_Token = str_replace($bearer, "", $headers['Authorization']); //取得Token
        }
        $headers = $this->input->request_headers('authorization');
        if (array_key_exists('authorization', $headers) && $headers['authorization'] != '') {
            $received_Token = str_replace($bearer, "", $headers['authorization']); //取得Token
        }
        //檢查token是否合法(存在於database)；
        $r = $this->common_service->checkToken($received_Token);
        if ($r['status']) {
            //檢查逾時
            $user_id = $r['data'][0]->id;
            $user_account = $r['data'][0]->account;
            $TC = $r['data'][0]->tokenCreateTime;
            $TU = $r['data'][0]->tokenUpdateTime;
            $TN = date('Y-m-d H:i:s'); //now
            $c_not_expired = $this->common_service->check_date_long($TC, $TN, TOKENEXPIRED);   //return true: 沒超過限制
            $u_not_expired = $this->common_service->check_date_long($TU, $TN, TOKENEXPIRED);   //return true: 沒超過限制
            if (($c_not_expired !== true) && $u_not_expired === true) {              //建立token的時間超過限制，但使用者持續使用，因此換發新的token
                $new_Token = $this->renewToken($user_id, $user_account);
                $r = $this->common_service->checkToken($new_Token['token']);    //重新取得使用者資訊                
            } elseif ($u_not_expired !== TRUE) {                                    //使用者idle時間已超過限制，token過期，登出user
                $this->deleteToken($received_Token);
                return array("status" => 2, "msg" => "token 不合法或逾時","authorization"=> $headers);
            } else {                                                              //正常，更新Token的T_UpdateDT
                $this->common_service->renewTokenUpdateDT($received_Token);
            }
            return array("status" => 1, "data" => $r['data'][0]);
        } else {  //token 不合法或逾時，導到登入頁面
            return array("status" => 0, "msg" => "token 不合法或逾時","authorization"=> $headers);
        }
    }

    public function checkAA_front(){
        $bearer = array("Bearer ", "bearer ", "BEARER ");     
        $received_Token = "";
        $headers = $this->input->request_headers();
        $headers = array_change_key_case($headers, CASE_LOWER);
        $authorization = "";
        if(array_key_exists('authorization', $headers)){
            $authorization = $headers['authorization'];
        }
        if(array_key_exists('Authorization', $headers)){
            $authorization = $headers['Authorization'];
        }
        if ($authorization != '') {
            $received_Token = str_replace($bearer, "", $authorization); //取得Token
        }

        //檢查token是否合法(存在於database)；
        if(empty($received_Token) || $received_Token == 'null'){
            return array("status" => 0, "msg" => "token 不合法或逾時","headers"=> $headers,"authorization"=> $authorization);
        }
        $r = $this->common_service->checkToken_front($received_Token);
        if ($r['status']) {
            //檢查逾時
            $user_id = $r['data'][0]->id;
            $user_account = $r['data'][0]->account;
            $TC = $r['data'][0]->tokenCreateTime;
            $TU = $r['data'][0]->tokenUpdateTime;
            $TN = date('Y-m-d H:i:s'); //now
            $c_not_expired = $this->common_service->check_date_long($TC, $TN, TOKENEXPIRED);   //return true: 沒超過限制
            $u_not_expired = $this->common_service->check_date_long($TU, $TN, TOKENEXPIRED);   //return true: 沒超過限制
            if (($c_not_expired !== true) && $u_not_expired === true) {              //建立token的時間超過限制，但使用者持續使用，因此換發新的token
                $new_Token = $this->renewToken_front($user_id, $user_account);
                $r = $this->common_service->checkToken_front($new_Token['token']);    //重新取得使用者資訊                
            } elseif ($u_not_expired !== TRUE) {                                    //使用者idle時間已超過限制，token過期，登出user
                $this->deleteToken_front($received_Token);
                return array("status" => 0, "msg" => "token 不合法或逾時","authorization"=> $headers);
            } else {                                                              //正常，更新Token的T_UpdateDT
                $this->common_service->renewTokenUpdateDT_front($received_Token);
            }
            return array("status" => 1, "data" => $r['data'][0]);
        } else {  //token 不合法或逾時，導到登入頁面
            return array("status" => 0, "msg" => "token 不合法或逾時","authorization"=> $headers);
        }
    }

    // 檢查字串是否英數混合
    public function check_string_validation($string){
        $lens = strlen($string); //取得字數
        $string= strtolower($string); //字串轉小寫
        $c_ok = 0;
        $n_ok = 0;
        for ($i=0; $i<$lens; $i++) {
            $cc = substr($string, $i, 1);
            $c_ok += substr_count("abcdefghijklmnopqrstuvwxyz", $cc); //字母出現的次數
            $n_ok += substr_count("1234567890", $cc); //數字出現的次數
        }
        if ($c_ok==0 || $n_ok==0 || ($n_ok+$c_ok != $lens)){
            $this->form_validation->set_message('check_string_validation', '{field} 必須英數混合');
            return FALSE;
        }
        return TRUE;
    }

    // 檢查日期時間格式
    public function timestamp_validation($dateTime)
    {
        if (date('Y-m-d H:i:s', strtotime($dateTime)) != $dateTime && date('Y/m/d H:i:s', strtotime($dateTime)) != $dateTime) {
            $this->form_validation->set_message('timestamp_validation', '{field} 格式錯誤');
            return FALSE;
        } else {
            return TRUE;
        }
    }

     // 檢查日期格式
    public function datetamp_validation($dateTime){
        if( date('Y-m-d', strtotime($dateTime)) != $dateTime && date('Y/m/d', strtotime($dateTime)) != $dateTime){
            $this->form_validation->set_message('datetamp_validation', '{field} 格式錯誤');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    // 檢查手機號碼格式(含國碼)
    public function phone_validation($fullphone){
        try{
            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumberObject = $phoneNumberUtil->parse($fullphone,null);
            if($phoneNumberUtil->isValidNumber($phoneNumberObject)){
                return TRUE;
            }else{
                $this->form_validation->set_message('phone_validation', '{field} 格式錯誤');
                return FALSE;
            }
        }catch(libphonenumber\NumberParseException $e){
            $this->form_validation->set_message('phone_validation', '{field} 格式錯誤');
            return FALSE;
        }
    }

    // 檢查裝置類型
    public function is_mobile_request(){
        // 如果有HTTP_X_WAP_PROFILE則一定是移動設備
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        // 如果via信息含有wap則一定是移動設備,部分服務商會屏蔽該信息
        if (isset ($_SERVER['HTTP_VIA'])){
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false; // 找不到為flase,否則為true
        }
        // 判斷手機發送的客戶端標誌,兼容性有待提高,把常見的類型放到前面
        if (isset ($_SERVER['HTTP_USER_AGENT'])){
            $clientkeywords = array (
                'android',
                'iphone',
                'samsung',
                'ucweb',
                'wap',
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'ipod',
                'blackberry',
                'meizu',
                'netfront',
                'symbian',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp'
            );
            // 從HTTP_USER_AGENT中查找手機瀏覽器的關鍵字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        // 協議法，因為有可能不准確，放到最後判斷
        if (isset ($_SERVER['HTTP_ACCEPT'])){
            // 如果只支持wml並且不支持html那一定是移動設備
            // 如果支持wml和html但是wml在html之前則是移動設備
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return true;
            }
        }
        return false;
    }
}