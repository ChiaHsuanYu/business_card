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
        if (isset($this->session->mgt_user_info)){                              //從 session 取得 token
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
            if ($c_not_expired !== TRUE && $u_not_expired === TRUE){              //建立token的時間超過限制，但使用者持續使用，因此換發新的token
                $new_Token = $this->renewToken($user_id,$user_account);
                $r = $this->common_service->checkToken($new_Token['token']);    //重新取得使用者資訊                
            }elseif($u_not_expired !== TRUE){                                    //使用者idle時間已超過限制，token過期，登出user
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

    // 檢查日期時間格式
    public function timestamp_validation($dateTime){
        if( date('Y-m-d H:i:s', strtotime($dateTime)) != $dateTime && date('Y/m/d H:i:s', strtotime($dateTime)) != $dateTime)
        {
            $this->form_validation->set_message('timestamp_validation', '{field} 格式錯誤');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    // 檢查日期格式
    public function datetamp_validation($dateTime){
        if( date('Y-m-d', strtotime($dateTime)) != $dateTime && date('Y/m/d', strtotime($dateTime)) != $dateTime)
        {
            $this->form_validation->set_message('timestamp_validation', '{field} 格式錯誤');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    // 檢查資料格式是否為JSON
    public function json_validation($str){
        $r = is_null(json_decode($str));
        if ($r) {
            $this->form_validation->set_message('json_validation', '{field} 須為JSON格式');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    // 檢查資料格式是否為純數字
    public function numeric_validation($str){
        $r = is_numeric($str);
        if ($r) {
            $this->form_validation->set_message('numeric_validation', '{field} 不可為數字');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
