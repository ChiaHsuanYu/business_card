<?php
class Register_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->service('Common_service');
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
            // 簡訊驗證發送
            $SMSNumber = 1;
            // $verifyCode = $this->common_service->GenRandomCode();
            $verifyCode = '123456';
            $message = "Business-card驗證碼： ".$verifyCode;
            $result = $this->sms_service->send_sms($account,$message);
            $send_data = array(
                'status'=> $result['status'],
                'mobile_number'=>$account,
                'msg' => $result['msg'],
            );
            if($result['status']){
                // 寫入驗證碼
                $this->users_model->update_verifyCode_by_id($verifyCode,$SMSNumber,$userId);
                $result = array(
                    "status" => 1,
                    "msg"=> "註冊成功，當日簡訊發送次數剩餘".(SMS_NUM-$SMSNumber)."次"  // (簡訊驗證發送功能待開發)
                );
            }else{
                $this->users_model->update_verifyCode_by_id($verifyCode,$SMSNumber,$userId);
                $result = array(
                    "status" => 1,
                    "msg"=> "驗證簡訊發送失敗(暫不開啟發送功能)，驗證碼請輸入".$verifyCode
                );  
            }
            log_message('error', '發送簡訊紀錄：'.json_encode($send_data));
        }
        return $result;
    }
}