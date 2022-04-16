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
        // 新增使用者
        $userId = $this->users_model->add_user($account);
        if(!$userId){
            $result = array(
                "status" => 0,
                "msg"=> "註冊失敗"
            );    
        }else{
            $this->session->account_data = array(
                'id'=>$userId
            );
            /*
            -------簡訊驗證發送功能待開發-------
            */ 

            // 寫入驗證碼
            $verifyCode = "123456";
            $r = $this->users_model->update_verifyCode_by_id($verifyCode,$userId);
            $result = array(
                "status" => 1,
                "msg"=> "註冊成功，簡訊驗證發送功能待開發"
            );  
        }
        return $result;
    }
}