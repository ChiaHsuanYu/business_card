<?php
class Login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Register_service");
        $this->load->service("Common_service");
        $this->load->service("Users_service");
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
                return $result; 
            }
            $this->session->account_data = array(
                'id'=> $r[0]->id
            );
            // 檢查當日簡訊發送次數及間隔時間
            $result = $this->check_smsnumber($r[0]->SMSNumber,$r[0]->SMSTime);
            if(!$result['status']){
                return $result;
            }
            $SMSNumber = $result['SMSNumber'];            
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
            // 新增發送紀錄
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
            return $result;
        }
        // 執行註冊動作
        $result = $this->register_service->register_account($account);
        return $result;
    }

    // 檢查當日簡訊發送次數及間隔時間
    public function check_smsnumber($SMSNumber,$SMSTime){
        // 判斷是否有上次發送時間
        $result = array(
            "status" => 1,
            "SMSNumber"=> 1
        );  
        if(!$SMSTime){
            return $result;
        }
        // 判斷當下時間是否大於上次發送時間
        $nowTime = date('Y-m-d H:i:s');
        $startDT_unix =  strtotime($SMSTime);
        $endDT_unix =  strtotime($nowTime);
        if ($endDT_unix >= $startDT_unix) {
            $interval = $endDT_unix - $startDT_unix;
            // 判斷發送間隔是否大於限制時間
            if ($interval < SMSEXPIRED) {
                $result = array(
                    "status" => 0,
                    "msg"=> "上次發送簡訊時間為".$SMSTime."，發送頻率需間隔一分鐘"
                );  
                return $result;
            }
        }
        if((SMS_NUM-$SMSNumber) < 1){
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
            $result['SMSNumber'] = 1;
        // }
        return $result;
    }

    // 帳號驗證
    public function account_verify($data){
        if(!$this->session->account_data){
            $result = array(
                "status" => 3,
                "msg"=> "尚未註冊手機號碼"
            );
            return $result;
        }
        $data['userId'] = $this->session->account_data['id'];
        $r = $this->users_model->check_verify_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "手機驗證失敗"
            );
            return $result;
        }
        $this->users_model->update_verify_by_id($data['userId']);
        $result = array(
            "status" => 1,
            "data"=> $r
        );
        return $result;
    }

    // 整理使用者資料 by companyId,userId
    public function get_company($user_data){
        // 整理資料-依照順序取得公司資訊 by companyId,userId
        $user_data = $this->users_service->sort_companyInfo($user_data);
        // 整理資料-取得社群資訊
        $user_data->personal_social = $this->users_service->get_social_data($user_data->personal_social);
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
        $google_client->setRedirectUri(GOOGLE_REDIRECT_URI); //Define your Redirect Uri
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
        if(!isset($output['access_token'])){
            $result = array(
                "status" => 0,
                "msg"=> "登入失敗",
            );
            return $result;
        }
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
        $output = json_decode($output, true);
        if(!isset($output['access_token'])){
            $result = array(
                "status" => 0,
                "msg"=> "登入失敗",
            );
            return $result;
        }
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
        if (!$r) {
            $result = array(
                "status" => 0,
                "msg"=> "驗證失敗"
            );
            return $result;
        }
        $result = array(
            "status" => 1,
            "data"=> $r
        );
        return $result;
    }

    // 登出
    public function logout($account){
        if ($account){
            $this->session->sess_destroy();
        }
    }
}