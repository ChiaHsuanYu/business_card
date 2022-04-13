<?php
class Register_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->service('common_service');
        $this->load->library('session');
    }
   
    // 註冊帳號並發送簡訊驗證
    public function register_account($account){
        // 檢查手機號碼是否已綁定
        $r = $this->users_model->check_account($account);
        if($r){
            $userId = $r[0]->id;
            if($r[0]->verify=='1'){
                $result = array(
                    "status" => 0,
                    "msg"=> "手機號碼已綁定，請重新輸入！"
                );  
                return $result; 
            }
        }else{
            $userId = $this->users_model->add_user($account);
            if(!$userId){
                $result = array(
                    "status" => 0,
                    "msg"=> "註冊失敗"
                );    
                return $result;
            }
        }

        if ($userId){
            /*
            -------簡訊驗證發送功能待開發-------
            */ 

            // 寫入驗證碼
            $verifyCode = "";
            $r = $this->users_model->update_verifyCode_by_id($verifyCode,$userId);
            $result = array(
                "status" => 0,
                "msg"=> "簡訊驗證發送功能待開發"
            );  
        }
        return $result;
    }

    // 帳號驗證
    public function account_verify($data){
        $r = $this->users_model->check_verify_by_account($data);
        if($r){
            $r = $this->users_model->update_verify_by_id($r[0]->id);
            $result = array(
                "status" => 1,
                "msg"=> "驗證成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "驗證失敗"
            );    
        }
        return $result;
    }
}