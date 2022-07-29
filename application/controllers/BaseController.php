<?php

require APPPATH . 'libraries/CreatorJwt.php';

class BaseController extends CI_Controller 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Common_service");
        $this->load->library('session');
        $this->objOfJwt = new CreatorJwt();
        header("X-Frame-Options: DENY");
    }

    public function GenToken($user_id, $user_account)
    {
        $tokenData['id'] = $user_id;
        $tokenData['account'] = $user_account;
        $tokenData['timeStamp'] = date('Y-m-d H:i:s');

        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        return array('Token'=>$jwtToken);
    }

    public function renewToken($user_id, $user_account)
    {
        $jwtToken = $this->GenToken($user_id, $user_account);

        //update db
        $this->common_service->renewTokenById($user_id,$jwtToken['Token']); //更新Token的T_UpdateDT

        return $jwtToken;
    }

    public function deleteToken($token)
    {
        return $this->common_service->removeToken($token);
    }

    // 檢查登入狀態 Authentication/使用權限 Authorization
    public function checkAA()
    {
        $received_Token = "";
        if (isset($this->session->mgt_user_info)){  //從 session 取得 token
            $received_Token = $this->session->mgt_user_info['token'];
        }
        //檢查token是否合法(存在於database)；
        $r = $this->common_service->checkToken($received_Token);
        if ($r['status']){
            //檢查逾時
            $user_id = $r['data'][0]->id;
            $user_account = $r['data'][0]->account;
            $TC = $r['data'][0]->tokenCreateTime;
            $TU = $r['data'][0]->tokenUpdateTime;
            $TN = date('Y-m-d H:i:s');                                                       //now
            $c_not_expired = $this->common_service->check_date_long($TC,$TN,TOKENEXPIRED);   //return true: 沒超過限制
            $u_not_expired = $this->common_service->check_date_long($TU,$TN,TOKENEXPIRED);   //return true: 沒超過限制
            if ($c_not_expired !== TRUE && $u_not_expired === TRUE){            //建立token的時間超過限制，但使用者持續使用，因此換發新的token
                $new_Token = $this->renewToken($user_id,$user_account);
                $r = $this->common_service->checkToken($new_Token['token']);    //重新取得使用者資訊                
            }elseif($u_not_expired !== TRUE){                                   //使用者idle時間已超過限制，token過期，登出user
                $this->deleteToken($received_Token);
                return array( "status" => 0, "message" => "token 不合法或逾時");
            }else{                                                              //正常，更新Token的T_UpdateDT
                $this->common_service->renewTokenUpdateDT($received_Token);
            }
            return array("status" => 1, "data" => $r['data'][0]);
        }else{  //token 不合法或逾時，導到登入頁面
            return array( "status" => 0, "message" => "token 不合法或逾時");
        }
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
    
    public function deleteToken_front($token)
    {
        return $this->common_service->removeToken_front($token);
    }
    
    public function checkAA_front($received_Token = ''){
        if(!$received_Token){
            $bearer = array("Bearer ", "bearer ", "BEARER ");     
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
        }
        //檢查token是否合法(存在於database)；
        if(empty($received_Token) || $received_Token == 'null'){
            return array("status" => 0, "msg" => "token 不合法或逾時");
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
                $r = $this->common_service->checkToken_front($new_Token['token']);   //重新取得使用者資訊                
            } elseif ($u_not_expired !== TRUE) {                                     //使用者idle時間已超過限制，token過期，登出user
                $this->deleteToken_front($received_Token);
                return array("status" => 0, "msg" => "token 不合法或逾時");
            } else {                                                                 //正常，更新Token的T_UpdateDT
                $this->common_service->renewTokenUpdateDT_front($received_Token);
            }
            return array("status" => 1, "data" => $r['data'][0]);
        } else {  //token 不合法或逾時，導到登入頁面
            return array("status" => 0, "msg" => "token 不合法或逾時");
        }
    }
    
    public function get_device_type(){
        $device = '0';
        $mobile = $this->is_mobile_request();
        if($mobile){
            $device = '1';
        }
        return $device;
    }

    // 檢查日期時間格式
    public function timestamp_validation($dateTime){
        if( date('Y-m-d H:i:s', strtotime($dateTime)) != $dateTime && date('Y/m/d H:i:s', strtotime($dateTime)) != $dateTime){
            $this->form_validation->set_message('timestamp_validation', '{field} 格式錯誤');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    // 檢查日期格式
    public function datetamp_validation($dateTime){
        if( date('Y-m-d', strtotime($dateTime)) != $dateTime && date('Y/m/d', strtotime($dateTime)) != $dateTime){
            $this->form_validation->set_message('timestamp_validation', '{field} 格式錯誤');
            return FALSE;
        }else{
            return TRUE;
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
            // 找不到為flase,否則為true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
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
