<?php
class Login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Register_service");
        $this->load->service("Common_service");
        $this->load->service("SMS_service");
        $this->load->model('users_model');
        $this->load->model('social_model');
        $this->load->model('sms_log_model');
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
                    // if($SMSDate[0]==$nowDate){
                    //     $SMSNumber = $SMSNumber + 1;
                    // }else{
                        $SMSNumber = 1;
                    // }
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
                $this->sms_log_model->add_sms_log($send_data);
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

    // google登入驗證
    public function google_login($code){
        $google_client = new Google_Client();
        $google_client->setClientId(GOOGLE_CLIENTID); //Define your ClientID
        $google_client->setClientSecret(GOOGLE_CLIENTSECRET); //Define your Client Secret Key
        $google_client->setRedirectUri('https://shine.sub.sakawa.com.tw/business_card/google_login/login'); //Define your Redirect Uri
        $google_client->addScope('email');
        $google_client->addScope('profile');
        $token = $google_client->fetchAccessTokenWithAuthCode($code);

        if (!isset($token["error"])) {
            $google_client->setAccessToken($token['access_token']);
            $this->session->set_userdata('access_token', $token['access_token']);
            $google_service = new Google_Service_Oauth2($google_client);
            // $google_service = new Google\Service\Oauth2($google_client);
            $data = $google_service->userinfo->get();
            if ($check_result = $this->users_model->check_user_by_google_uid($data['id'])) {
                if($check_result[0]->isDeleted == '1'){
                    $result = array(
                        "status" => 0,
                        "msg"=> "帳戶已被凍結"
                    );
                    return $result;
                }
                $this->users_model->update_google_access_token($token['access_token'], $data['id']);
            } else {
                //insert data
                $user_data = array(
                    'google_uid' => $data['id'],
                    'name' => $data['family_name'].$data['given_name'],
                    'email' => $data['email'],
                    'google_access_token' => $token['access_token'],
                    'avatar' => $data['picture'],
                );
                $this->users_model->add_google_user($user_data);
            }
        }
        if (!$this->session->userdata('access_token')) {
            $result = array(
                "status" => 0,
                "msg"=> "登入失敗"
            );
        }else{
            $google_client->revokeToken();
            $result = array(
                "status" => 1,
                "msg"=> "登入成功",
            );
        }
        return $result;
    }

    public function get_google_login_data($access_token){
        if ($r = $this->users_model->get_user_by_google_access_token($access_token)) {
            $result = array(
                "status" => 1,
                "data"=> $r
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "驗證失敗"
            );
        }
        return $result;
    }

    public function facebook_login($code){
        // 取得 access_token
        $url = $this->config->item('facebook_access_token_api').
                "?client_id=".$this->config->item('facebook_app_id').
                "&redirect_uri=".$this->config->item('facebook_login_redirect_url').
                "&client_secret=".$this->config->item('facebook_app_secret').
                "&code=".$code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        if(isset($output['access_token'])){
            // 存取token並取得使用者資訊
            $this->session->set_userdata('fb_access_token', $output['access_token']); 
            $this->facebook->setAccessToken($output['access_token']);
            $fbUser = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,picture',[],$output['access_token']); 
            
            $fb_uid = !empty($fbUser['id'])?$fbUser['id']:'';
            if ($check_result = $this->users_model->check_user_by_facebook_uid($fb_uid)) {
                if($check_result[0]->isDeleted == '1'){
                    $result = array(
                        "status" => 0,
                        "msg"=> "帳戶已被凍結"
                    );
                    return $result;
                }
                $this->users_model->update_facebook_access_token($output['access_token'], $fb_uid);
            } else {
                //insert data
                $user_data = array(
                    'facebook_uid' => $fb_uid,
                    'name' => (!empty($fbUser['first_name'])?$fbUser['first_name']:'') . (!empty($fbUser['last_name'])?$fbUser['last_name']:''),
                    'email' => !empty($fbUser['email'])?$fbUser['email']:'',
                    'facebook_access_token' => $output['access_token'],
                    'avatar' => !empty($fbUser['picture']['data']['url'])?$fbUser['picture']['data']['url']:'',
                );
                $this->users_model->add_facebook_user($user_data);
            }
            $result = array(
                "status" => 1,
                "msg"=> "登入成功"
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "登入失敗",
            );
        }
        return $result;
    }
    
    public function line_login($code){
        // 取得 access_token
        $url = LINE_ACCESS_TOKEN_API;
        $postData = array(
            "grant_type" => 'authorization_code',
            "code" => $code,
            "redirect_uri" => LINE_LOGIN_REDIRECT_URL,
            "client_id" => LINE_APP_ID,
            "client_secret" => LINE_APP_SECRET
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        print_r($output);
        $output = json_decode($output, true);
        if(isset($output['access_token'])){
           
            // 存取token並取得使用者資訊
            $this->session->set_userdata('line_access_token', $output['access_token']); 
            $url = LINE_PROFILE_API;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$output['access_token'],
            ));
            $profile = curl_exec($ch);
            curl_close($ch);
            print_r($profile);
            $profile = json_decode($profile, true);
            
            $line_uid = !empty($profile['userId'])?$profile['userId']:'';
            if ($check_result = $this->users_model->check_user_by_line_uid($line_uid)) {
                if($check_result[0]->isDeleted == '1'){
                    $result = array(
                        "status" => 0,
                        "msg"=> "帳戶已被凍結"
                    );
                    return $result;
                }
                $this->users_model->update_line_access_token($output['access_token'], $line_uid);
            } else {
                // insert data
                $user_data = array(
                    'line_uid' => $line_uid,
                    'name' => (!empty($profile['displayName'])?$profile['displayName']:''),
                    'line_access_token' => $output['access_token'],
                    'avatar' => !empty($profile['pictureUrl'])?$profile['pictureUrl']:'',
                );
                $this->users_model->add_line_user($user_data);
            }
            $result = array(
                "status" => 1,
                "msg"=> "登入成功",
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "登入失敗",
            );
        }
        return $result;
    }

    public function get_social_login_data($social_type,$access_token){
        switch($social_type){
            case 1:
                $r = $this->users_model->get_user_by_google_access_token($access_token);
                break;
            case 2:
                $r = $this->users_model->get_user_by_facebook_access_token($access_token);
                break;
            case 3:
                $r = $this->users_model->get_user_by_line_access_token($access_token);
                break;
            default:
                break;
        }
        if ($r) {
            $result = array(
                "status" => 1,
                "data"=> $r
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "驗證失敗"
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
}