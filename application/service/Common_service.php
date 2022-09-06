<?php
class Common_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        // $this->load->library('phpmailer_lib');
        $this->load->model('users_model');
        $this->load->model('mgt_users_model');
        $this->load->model('company_model');
        $this->load->model('token_model');
        $this->load->driver('cache');
    }

    // 檢查日期時間區間
    public function check_date_long($startDT, $endDT, $time_long){
        $startDT_unix =  strtotime($startDT);
        $endDT_unix =  strtotime($endDT);
        // 判斷結束時間是否大於開始時間
        if ($endDT_unix >= $startDT_unix) {
            $interval = $endDT_unix - $startDT_unix;
            // 判斷查詢區間是否大於1小時
            if ($interval > $time_long) {
                return "搜尋區間超過限制";
            } else {
                return true;
            }
        } else {
            return "結束時間不可小於開始時間";
        }
    }

    // 檢查資料庫是否有使用者 Token
    public function checkToken($token){
        $r = $this->mgt_users_model->get_user_by_token($token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "查詢失敗"
            );
        }
        return $result;
    }

    // 檢查資料庫是否有使用者 Token
    public function checkToken_front($token){
        $r = $this->token_model->get_user_by_token($token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "查詢失敗"
            );
        }
        return $result;
    }

    // 更新Token 的 update time
    public function renewTokenUpdateDT($token){
        $r = $this->mgt_users_model->update_TUpdateDT_by_token($token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "更新失敗"
            );
        }
        return $result;
    }

    // 更新Token 的 update time
    public function renewTokenUpdateDT_front($token){
        $r = $this->token_model->update_TUpdateDT_by_token($token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "更新失敗"
            );
        }
        return $result;
    }

    // 更新 Token, T_CreateDT, T_UpdateDT
    public function renewTokenById($user_id, $token){
        $r = $this->mgt_users_model->update_Token_by_id($user_id, $token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "更新失敗"
            );
        }
        return $result;
    }

    public function renewTokenById_front($user_id, $token, $host, $device){
        $r = $this->token_model->check_host_by_userId($user_id,$host,$device);
        if($r){
            $r = $this->token_model->update_Token_by_id($r[0]->id, $token);
        }else{
            $r = $this->token_model->add_token($user_id,$token,$host,$device);
        }
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "更新失敗"
            );
        }
        return $result;
    }

    //更新Token為NULL
    public function removeToken($token){
        $r = $this->mgt_users_model->update_Token_as_NULL($token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "更新失敗"
            );
        }
        return $result;
    }

    //更新Token為NULL
    public function removeToken_front($token){
        $r = $this->token_model->update_Token_as_NULL($token);
        if ($r) {
            $result = array(
                "status" => 1,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => 0,
                "message" => "更新失敗"
            );
        }
        return $result;
    }

    // 限制設備裝置登入數量
    public function restrict_user_device($user_id,$device){
        // 取得已登入設備裝置數量
        $r = $this->token_model->get_login_device($user_id,$device);
        if($r){
            $device_num = count($r);
            if($device_num >= LOGIN_DEVICE_NUM){
                for($i=0; $i<$device_num;$i++){
                    // 數量超出限制，強制將衝突裝置登出
                    $this->token_model->update_Token_as_NULL($r[$i]->token);
                }
                $result = array(
                    "status" => 0,
                    "message" => "同一設備類型僅能同時登入一台，已將衝突裝置登出"
                );
                return $result;
            }
        }
        $result = array(
            "status" => 1,
            "message" => "無衝突裝置"
        );
        return $result;
    }

    //產生隨機密碼
    public function GenRandomPWD()
    {
        $length = 10;
        //隨機密碼可能包含的字符
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($str), 0, $length);
        return $password;
    }

    //產生隨機驗證碼
    public function GenRandomCode()
    {
        $length = 6;
        //隨機密碼可能包含的字符
        // $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "0123456789";
        $verifyCode = substr(str_shuffle($str), 0, $length);
        return $verifyCode;
    }

    //發送信件
    /*
        SMTP、寄件者等資訊在 application/libraries/Phpmailer_lib.php 裡面設定
    */
    // public function SendMail($to, $cc, $subject, $isHTML, $content, $path = FALSE, $filename = FALSE)
    // {
    //     $EmailFormatIsCorrect = true;

    //     // PHPMailer object
    //     $mail = $this->phpmailer_lib->load();

    //     $mail_send = "";
    //     $to = explode(";", $to);
    //     for ($i = 0; $i < count($to); $i++) {
    //         $mail_send = $to[$i];
    //         //判斷email是否符合格式，如果不符合回傳錯誤訊息
    //         if (preg_match("/^([\w\.\-]){1,64}\@([\w\.\-]){1,64}$/", $mail_send)) {
    //             $mail->AddAddress("$mail_send", "$mail_send");
    //         } else {
    //             $EmailFormatIsCorrect = false;
    //         }
    //     }

    //     $mail_send = "";
    //     if (trim($cc) != "") {
    //         $cc = explode(";", $cc);
    //         for ($i = 0; $i < count($cc); $i++) {
    //             $mail_send = $cc[$i];
    //             //判斷email是否符合格式，如果不符合回傳錯誤訊息
    //             if (preg_match("/^([\w\.\-]){1,64}\@([\w\.\-]){1,64}$/", $mail_send)) {
    //                 $mail->AddAddress("$mail_send", "$mail_send");
    //             } else {
    //                 $EmailFormatIsCorrect = false;
    //             }
    //         }
    //     }

    //     if (!$EmailFormatIsCorrect) {
    //         $result = array(
    //             "status" => 0,
    //             "message" => "Email格式錯誤"
    //         );
    //         return $result;
    //     }

    //     // Email subject
    //     $mail->Subject = $subject;

    //     // Set email format to HTML
    //     $mail->isHTML($isHTML);

    //     // Email body content
    //     $mailContent = $content;
    //     $mail->Body = $mailContent;

    //     // 判斷有無附件檔
    //     if ($filename) {
    //         $mail->AddAttachment($path . $filename, $filename); // 設定附件檔檔名
    //     }

    //     // Send email
    //     if (!$mail->send()) {
    //         $result = array(
    //             "status" => 0,
    //             "message" => $mail->ErrorInfo
    //         );
    //         return $result;
    //     } else {
    //         $result = array(
    //             "status" => 1,
    //             "message" => "Email發送成功"
    //         );
    //         return $result;
    //     }
    // }

    //時間格式轉換(補齊00:00:00)
    public function timeTransform($time){
        $hour = floor($time % 86400 / 3600);
        if ($hour < 10) {
            $hour = "0" . $hour;
        }
        $minute = floor($time % 86400 / 60 % 60);
        if ($minute < 10) {
            $minute = "0" . $minute;
        }
        $second = floor($time % 86400 % 60);
        if ($second < 10) {
            $second = "0" . $second;
        }
        return $hour . ":" . $minute . ":" . $second;
    }

    //取得UUID
    public function create_uuid(){
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $uuid;
    }

    public function avatar_uuid($fileName){
        $name = explode('.', $fileName);
        $nowtime = date('YmdHis');
        $userId = $this->session->user_info['id'];
        $new_num = 1; //預設1，總數+1=新單號
        $uuid = $userId."_".$nowtime."_".$new_num;
        $path = DEL_AVATAR_PATH;
        if (!is_dir($path)) {
            mkdir($path, 0755);
        }
        while(file_exists(AVATAR_PATH.$uuid.".".$name[1]) || file_exists(DEL_AVATAR_PATH.$uuid.".".$name[1])){
            $new_num++;
            $uuid = $userId."_".$nowtime."_".$new_num;
        }
        return $uuid;
    }

    public function logo_uuid($fileName){
        $name = explode('.', $fileName);
        $nowtime = date('YmdHis');
        $userId = $this->session->user_info['id'];
        $new_num = 1; //預設1，總數+1=新單號
        $uuid = "logo_".$userId."_".$nowtime."_".$new_num;
        $path = DEL_LOGO_PATH;
        if (!is_dir($path)) {
            mkdir($path, 0755);
        }
        while(file_exists(LOGO_PATH.$uuid.".".$name[1]) || file_exists(DEL_LOGO_PATH.$uuid.".".$name[1])){
            $new_num++;
            $uuid = "logo_".$userId."_".$nowtime."_".$new_num;
        }
        return $uuid;
    }

    //寫入Log
    public function logger($desc){
        $user_id = 0;
        if (isset($this->session->mgt_user_info)){  //從 session 取得 userID
            $user_id = $this->session->mgt_user_info['id'];
        }
        $log_data = array(
            'user_id'=>$user_id,
            'router_class'=>$this->router->class,
            'router_method'=>$this->router->method,
            'desc'=>$desc,
        );
        // backstage_log
        log_message('error', '後台操作紀錄：'.json_encode($log_data));
    }

    //取得client端IP
    public function get_ip(){
        $host = "";
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $host = $_SERVER['HTTP_CLIENT_IP'];
        }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $host = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $host= $_SERVER['REMOTE_ADDR'];
        }
        return $host;
    }

    // 陣列轉字串
    public function str_implode($separator,$array){
        $str = null;
        if($array){
            $str = implode($separator,$array);
        }
        return $str;
    }

    // 陣列轉JSON
    public function str_json_encode($str){
        $str_json_encode = null;
        if($str){
            $str_json_encode = json_encode($str);
        }
        return $str_json_encode;
    }

    // 緩存通知訊息
    public function add_notify_cache($userId,$data){
        $all_notify_data = $this->cache->redis->get('notify_list'); //取得其他通知緩存
        $notify_data = array();
        if(array_key_exists('notify_'.$userId,$all_notify_data)){
            $notify_data = $all_notify_data['notify_'.$userId];
        }
        array_push($notify_data,$data);
        $all_notify_data['notify_'.$userId] = $notify_data;
        $this->cache->redis->save('notify_list',$all_notify_data,NOTIFY_TIME_TO_LIVE); //記錄緩存並設置存活時間
    }

    // 取得通知緩存訊息
    public function check_notify(){
        $userId = $this->session->user_info['id'];
        $notify_data = $this->cache->redis->get('notify_'.$userId); //取得通知緩存
        if($notify_data){
            $this->cache->redis->delete('notify_'.$userId); //刪除緩存
        }
        return $notify_data;
    }
}
