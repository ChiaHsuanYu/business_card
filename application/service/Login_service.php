<?php
class Login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("register_service");
        $this->load->service("common_service");
        $this->load->service("sms_service");
        $this->load->model('users_model');
        $this->load->model('social_model');
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
                    $startDT_unix =  strtotime($SMSTime);
                    $endDT_unix =  strtotime($nowTime);
                    // 判斷結束時間是否大於開始時間
                    if ($endDT_unix >= $startDT_unix) {
                        $interval = $endDT_unix - $startDT_unix;
                        if ($interval < SMSEXPIRED) {
                            $result = array(
                                "status" => 0,
                                "msg"=> "上次發送簡訊時間為".$SMSTime."，發送頻率需間隔一分鐘"
                            );  
                            return $result;
                        }
                    }
                    $last_SMSNumber = SMS_NUM-$SMSNumber;
                    if($last_SMSNumber < 1){
                        $result = array(
                            "status" => 0,
                            "msg"=> "當日簡訊發送次數已達上限"
                        );  
                        return $result;
                    }
                    $SMSDate = explode(" ",$SMSTime);
                    $nowDate = date('Y-m-d');
                    if($SMSDate[0]==$nowDate){
                        $SMSNumber = $SMSNumber + 1;
                    }else{
                        $SMSNumber = 1;
                    }
                }else{
                    $SMSNumber = 1;
                }
                // 簡訊驗證發送
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
                    $this->users_model->update_verifyCode_by_id($verifyCode,$SMSNumber,$r[0]->id);
                    $result = array(
                        "status" => 1,
                        "msg"=> "手機號碼合法，當日簡訊發送次數剩餘".(SMS_NUM-$SMSNumber)."次"  // (簡訊驗證發送功能待開發)
                    );
                }else{
                    $this->users_model->update_verifyCode_by_id($verifyCode,$SMSNumber,$r[0]->id);
                    $result = array(
                        "status" => 1,
                        "msg"=> "驗證簡訊發送失敗(暫不開啟發送功能)，驗證碼請輸入".$verifyCode
                    );  
                }
                log_message('error', '發送簡訊紀錄：'.json_encode($send_data));
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