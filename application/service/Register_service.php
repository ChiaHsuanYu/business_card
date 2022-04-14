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
        // 檢查手機號碼是否已存在
        $r = $this->users_model->check_account($account);
        if($r){
            $userId = $r[0]->id;
            // 檢查驗證狀態
            if($r[0]->verify=='1'){
                // 檢查密碼是否已設定
                if($r[0]->password){
                    $result = array(
                        "status" => 0,
                        "msg"=> "手機號碼已綁定，請重新輸入！"
                    );
                }else{
                    $result = array(
                        "status" => 3,
                        "msg"=> "手機已綁定，前往設定密碼"
                    );  
                }
            }else{
                $this->session->account_data = array(
                    'id'=>$userId
                );
                $result = array(
                    "status" => 2,
                    "msg"=> "手機已註冊但尚未驗證\n今日簡訊驗證碼已發送x次，是否重新發送?"
                );
            }
        }else{
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
        }
        return $result;
    }

    // 帳號驗證
    public function account_verify($data){
        if($this->session->account_data){
            $data['userId'] = $this->session->account_data['id'];
            // 檢查使用者驗證狀態
            $r = $this->users_model->check_account_by_id($data['userId']);
            if($r[0]->verify){
                $result = array(
                    "status" => 3,
                    "msg"=> "手機已有綁定紀錄，直接前往設定密碼"
                );
                return $result;
            }
            $r = $this->users_model->check_verify_by_id($data);
            if($r){
                $r = $this->users_model->update_verify_by_id($data['userId']);
                $result = array(
                    "status" => 1,
                    "msg"=> "手機綁定成功，前往設定密碼"
                );
            }else{
                $result = array(
                    "status" => 0,
                    "msg"=> "手機驗證失敗"
                );
            }
        }else{
            $result = array(
                "status" => 4,
                "msg"=> "尚未註冊手機號碼"
            );
        }
        return $result;
    }

    // 設定密碼 by id
    public function add_password($data){
        if($this->session->account_data){
            $data['id'] = $this->session->account_data['id'];
            // 檢查使用者驗證狀態
            $r = $this->users_model->check_account_by_id($data['id']);
            if(!$r[0]->verify){
                $result = array(
                    "status" => 2,
                    "msg"=> "手機已註冊但尚未驗證\n今日簡訊驗證碼已發送x次，是否重新發送?"
                );
                return $result;
            }
            // 設定密碼
            $r = $this->users_model->update_password_by_id($data);
            if($r){
                $result = array(
                    "status" => 1,
                    "msg"=> "設定成功"
                );  
            }else{
                $result = array(
                    "status" => 0,
                    "msg"=> "設定失敗"
                );    
            }
        }else{
            $result = array(
                "status" => 4,
                "msg"=> "尚未註冊手機號碼"
            );
        }
        return $result;
    }
    
}