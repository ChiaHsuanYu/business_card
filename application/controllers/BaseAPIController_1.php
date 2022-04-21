<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/CreatorJwt.php';

class BaseAPIController_1 extends RestController {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->service("Common_service");
        $this->objOfJwt = new CreatorJwt();
        // header("Access-Control-Allow-Origin: https://192.168.88.123");
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            
            // header("Access-Control-Allow-Origin: https://192.168.88.123:8080");
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
        header('Content-Type: application/x-www-form-urlencoded');
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

    public function GenToken_front($user_id, $user_account, $host)
    {
        $tokenData['id'] = $user_id;
        $tokenData['account'] = $user_account;
        $tokenData['host'] = $host;
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
        $host = $this->input->request_headers()['x-forwarded-for'];
        $jwtToken = $this->GenToken_front($user_id, $user_account,$host);

        //update db
        $this->common_service->renewTokenById_front($user_id, $jwtToken['Token'],$host); //更新 Token, T_CreateDT, T_UpdateDT

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

    

    // 檢查登入狀態 Authentication/使用權限 Authorization
    public function checkAA(){
        // $received_Token = null;
        // if($this->session->mgt_user_info){
        //     $received_Token = $this->session->mgt_user_info['token'];
        // }
        $bearer = array("Bearer ", "bearer ", "BEARER ");     
        $received_Token = "";
        $headers = $this->input->request_headers('Authorization');
        if (array_key_exists('Authorization', $headers) && $headers['Authorization'] != '') {
            $received_Token = str_replace($bearer, "", $headers['Authorization']); //取得Token
        }
        // return $headers;
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
                return array("status" => 2, "msg" => "token 不合法或逾時");
            } else {                                                              //正常，更新Token的T_UpdateDT
                $this->common_service->renewTokenUpdateDT($received_Token);
            }
            return array("status" => 1, "data" => $r['data'][0]);
        } else {  //token 不合法或逾時，導到登入頁面
            return array("status" => 0, "msg" => "token 不合法或逾時");
        }
    }

    public function checkAA_front(){
        $bearer = array("Bearer ", "bearer ", "BEARER ");     
        $received_Token = "";
        $headers = $this->input->request_headers('Authorization');
        if (array_key_exists('Authorization', $headers) && $headers['Authorization'] != '') {
            $received_Token = str_replace($bearer, "", $headers['Authorization']); //取得Token
        }
        // return $headers;
        //檢查token是否合法(存在於database)；
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
                return array("status" => 0, "msg" => "token 不合法或逾時");
            } else {                                                              //正常，更新Token的T_UpdateDT
                $this->common_service->renewTokenUpdateDT_front($received_Token);
            }
            return array("status" => 1, "data" => $r['data'][0]);
        } else {  //token 不合法或逾時，導到登入頁面
            return array("status" => 0, "msg" => "token 不合法或逾時");
        }
    }

    // public function GetTokenData($received_Token)
    // {
    //     try {
    //         $jwtData = $this->objOfJwt->DecodeToken($received_Token['Authorization']);
    //         return json_encode($jwtData);
    //     } catch (Exception $e) {
    //         return array("status" => 0, "msg" => "Token錯誤");
    //     }
    // }

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
        if( date('Y-m-d', strtotime($dateTime)) != $dateTime && date('Y/m/d', strtotime($dateTime)) != $dateTime)
        {
            $this->form_validation->set_message('datetamp_validation', '{field} 格式錯誤');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function is_mobile_request(){
        // $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : ''; 
        // $mobile_browser = '0'; 
        // if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        //     $mobile_browser++; 
        // }
        // if((isset($_SERVER['HTTP_ACCEPT'])) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)) {
        //     $mobile_browser++; 
        // }
        // if(isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        //     $mobile_browser++; 
        // }
        // if(isset($_SERVER['HTTP_PROFILE'])) {
        //     $mobile_browser++; 
        //     $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)); 
        //     $mobile_agents = array( 'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac', 'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno', 'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-', 'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-', 'newt','noki','oper','palm','pana','pant','phil','play','port','prox', 'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar', 'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-', 'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp', 'wapr','webc','winw','winw','xda','xda-' ); 
        // }
        // if(in_array($mobile_ua, $mobile_agents)) {
        //     $mobile_browser++; 
        // }
        // if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
        //     $mobile_browser++; // Pre-final check to reset everything if the user is on Windows if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) $mobile_browser=0; // But WP7 is also Windows, with a slightly different characteristic if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) $mobile_browser++; if($mobile_browser>0) return true; else return false; 
        // }    
        // if($mobile_browser>0){
        //     return true; 
        // }else{
        //     return false;
        // }    
        return $_SERVER['user-agent'];
    }

    public function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE則一定是移動設備
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap則一定是移動設備,部分服務商會屏蔽該信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到為flase,否則為true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 判斷手機發送的客戶端標誌,兼容性有待提高,把常見的類型放到前面
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
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
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 協議法，因為有可能不准確，放到最後判斷
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml並且不支持html那一定是移動設備
            // 如果支持wml和html但是wml在html之前則是移動設備
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }
}