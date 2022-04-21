<?php
class Login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("register_service");
        $this->load->model('users_model');
        $this->load->model('social_model');
    }
    
    // 檢查是否已有登入紀錄
    public function check_login(){
        if($this->session->user_info){
            $account = $this->session->user_info['account'];
            if ($r = $this->users_model->get_user_by_acc($account)){
                if($r[0]->isDeleted){
                    $result = array(
                        "status" => 0,
                        "msg"=> "手機號碼已被凍結"
                    );    
                }else{
                    if($r[0]->tokenUpdateTime){
                        $TU = $r[0]->tokenUpdateTime;
                        $TN = date('Y-m-d H:i:s'); //now
                        $u_not_expired = $this->common_service->check_date_long($TU, $TN, TOKENEXPIRED);   //return true: 沒超過限制
                        if($u_not_expired){
                            // 已有登入紀錄，直接導向主頁
                            $result = array(
                                "status" => 2,
                                "data"=> $r
                            );
                            return $result;
                        }
                    }
                }
            }
        }
        $result = array(
            "status" => 1,
            "msg"=> "尚無登入紀錄"
        );   
        return $result;
    }

    // 檢查是否已有登入紀錄
    public function check_login_2($token){
        if($token){
            if ($r = $this->token_model->get_user_by_token($token)){
                if($r[0]->isDeleted){
                    $result = array(
                        "status" => 0,
                        "msg"=> "手機號碼已被凍結"
                    );    
                }else{
                    if($r[0]->tokenUpdateTime){
                        $TU = $r[0]->tokenUpdateTime;
                        $TN = date('Y-m-d H:i:s'); //now
                        $u_not_expired = $this->common_service->check_date_long($TU, $TN, TOKENEXPIRED);   //return true: 沒超過限制
                        if($u_not_expired){
                            // 已有登入紀錄，直接導向主頁
                            $result = array(
                                "status" => 2,
                                "data"=> $r
                            );
                            return $result;
                        }
                    }
                }
            }
        }
        $result = array(
            "status" => 1,
            "msg"=> "尚無登入紀錄"
        );   
        return $result;
    }

    public function login($account){
        // 取得帳號資訊
        if ($r = $this->users_model->get_user_by_acc($account)){
            if($r[0]->isDeleted){
                $result = array(
                    "status" => 0,
                    "msg"=> "手機號碼已被凍結"
                );    
            }else{
                $this->session->account_data = array(
                    'id'=> $r[0]->id
                );
                // 檢查當日簡訊發送次數及間隔時間
                $SMSNumber = $r[0]->SMSNumber;
                $SMSTime = $r[0]->SMSTime;
                if($SMSTime){
                    $nowTime = date('Y-m-d H:i:s');
                    $sms_not_expired = $this->common_service->check_date_long($SMSTime, $nowTime, SMSEXPIRED);   //return true: 沒超過限制
                    if($sms_not_expired){
                        $result = array(
                            "status" => 0,
                            "msg"=> "上次發送簡訊時間為".$SMSTime."，發送頻率需間隔一分鐘"
                        );  
                        return $result;
                    }
                    $last_SMSNumber = SMS_NUM-$SMSNumber;
                    if($last_SMSNumber < 1){
                        $result = array(
                            "status" => 0,
                            "msg"=> "當日簡訊發送次數已達上限"
                        );  
                        return $result;
                    }
                }
                /*
                    -------簡訊驗證發送功能待開發-------
                    // 發送後須更改資料表當日發送次數
                */ 
                // 寫入驗證碼
                $verifyCode = "123456";
                $this->users_model->update_verifyCode_by_id($verifyCode,$r[0]->id);
                $result = array(
                    "status" => 1,
                    "msg"=> "手機號碼合法，當日簡訊發送次數剩餘n次(簡訊驗證發送功能待開發)"
                );  
            }
        }else{
            // 執行註冊動作
            $result = $this->register_service->register_account($account);
        }
        return $result;
    }

    // 帳號驗證
    public function account_verify($data){
        if($this->session->account_data){
            $data['userId'] = $this->session->account_data['id'];
            $r = $this->users_model->check_verify_by_id($data);
            if($r){
                $this->users_model->update_verify_by_id($data['userId']);
                $result = array(
                    "status" => 1,
                    "data"=> $r
                );
            }else{
                $result = array(
                    "status" => 0,
                    "msg"=> "手機驗證失敗"
                );
            }
        }else{
            $result = array(
                "status" => 3,
                "msg"=> "尚未註冊手機號碼"
            );
        }
        return $result;
    }

    // 登出
    public function logout($account){
        if ($account){
            $this->session->sess_destroy();
        }
    }

    // 整理使用者資料 by companyId,userId
    public function get_company($user_data){
        $user_data->companyInfo = array();
        // 依照順序取得公司資訊
        if($user_data->companyOrder){
            for($i=0;$i<count($user_data->companyOrder);$i++){
                $companyId = $user_data->companyOrder[$i];
                $company_data = $this->company_model->get_company_by_userId($companyId,$user_data->id);
                if(count($company_data)){
                    // 依序取得公司社群icon
                    if($company_data[0]->company_social){
                        for($j=0;$j<count($company_data[0]->company_social);$j++){
                            $socialId = $company_data[0]->company_social[$j]->socialId;
                            $social_data = $this->social_model->get_social_by_id($socialId);
                            if(count($social_data)){
                                $company_data[0]->company_social[$j]->iconURL = $social_data[0]->iconURL;
                                $company_data[0]->company_social[$j]->socialName = $social_data[0]->name;
                            }
                        }
                    }
                    array_push($user_data->companyInfo,$company_data[0]);
                }
            }
        }
        // 依序取得個人社群icon
        if($user_data->personal_social){
            for($i=0;$i<count($user_data->personal_social);$i++){
                $socialId = $user_data->personal_social[$i]->socialId;
                $social_data = $this->social_model->get_social_by_id($socialId);
                if(count($social_data)){
                    $user_data->personal_social[$i]->iconURL = $social_data[0]->iconURL;
                    $user_data->personal_social[$i]->socialName = $social_data[0]->name;
                }
            }
        }
        $userInfo =  array(
            'userInfo'=>$user_data
        );
        return $userInfo;
    }
}