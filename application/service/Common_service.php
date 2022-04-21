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
    }

    // 檢查日期時間區間
    public function check_date_long($startDT, $endDT, $time_long)
    {
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
        
        $r = $this->token_model->check_host_by_userId($user_id,$host);
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
}
