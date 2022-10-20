<?php

/* 名稱：hiAir Send Text For PHP範例程式
 * 撰寫者 : HiNet - hiAir , Chih-Ming Liao
 * 撰寫日期 : 2006/06/27
 * 修改者 : HiNet - hiAir , Mike
 * 修改日期 : 2020/09/03
 * 備註 :
 * 重要提醒 : 如欲傳送多筆簡訊，連線成功後使用迴圈執行$mysms->send_text()即可
 */
include APPPATH. 'third_party/sms/sms2.inc';
class SMS_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
    }

    // 發送簡訊
    public function send_sms($mobile_number,$message){
        // error_reporting(E_ALL);
        $url = SEND_SMS_API;
        $data_base = array(
            // "username" => SMS_USERNAME,
            "password" => SMS_PASSWORD,
        );
       
        $data = array_merge($data_base, array(
            "mobile" => $mobile_number,
            "message" => $message,
        ));
       
        // 初始化 curl
        $request = curl_init();
        // 設定 curl
        $timeout = 60;
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_POST, true);  // 若以 POST 發送 request 需設為 true。
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);  // 設為 true，curl 就只會將結果傳回，不會輸出在畫面上。
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, $timeout);  // request timeout 設定60秒
        // 設定 Header
        $header = array(
            'Content-Type: application/json; charset=UTF-8',
        );
        curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        // 發送請求並轉換回傳結果
        $result = json_decode(curl_exec($request), true);
        // 取得 http status code
        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        // 關閉 curl
        curl_close($request);

        if ($http_code == 200 && is_array($result) && array_key_exists("ret_code", $result) && array_key_exists("ret_content", $result)) {
            $result = array(
                "status" => 1,
                "connect"=> "連線成功",
                "msg"=> $result["ret_content"],
            );
        } else {
            $ret_content = '';
            if (is_array($result) && array_key_exists("ret_code", $result) && array_key_exists("ret_content", $result)) {
                $ret_content = $result["ret_content"];
            }
            $result = array(
                "status" => 0,
                "connect"=> "連線失敗",
                "msg"=> $ret_content,
            );
        }
        return $result;
    }
    // 發送簡訊(中華電信尚未提供API時的舊版寫法)
    // public function send_sms($mobile_number,$message){
    //     error_reporting(E_ALL);

    //     /* Socket to Air Server IP ,Port */
    //     $server_ip = '202.39.54.130';
    //     $server_port = 8000;
    //     $TimeOut = 10;

    //     $user_acc = "";
    //     // $user_acc = "89881560";
    //     $user_pwd = "qwe55664";
    //     //static encoding number
    //     $ENCODING_BIG5 = 1;
    //     $ENCODING_UCS2 = 3;
    //     $ENCODING_UTF8 = 4;

    //     //簡訊內容編碼轉換
    //     $message_encodeFrom = "utf-8"; //請確認簡訊內容編碼 "utf-8" or "big5" or "ucs-2" or other encodings
    //     $target_encoding = $ENCODING_BIG5; //可選擇使用哪種編碼傳送
    //     if ($target_encoding == $ENCODING_BIG5) {
    //         $message = mb_convert_encoding($message, "big5", $message_encodeFrom);
    //         //mb_convert_encoding(message,encodeTo,encodeFrom), encodeTo:使用哪種編碼傳送, encodeFrom:簡訊內容編碼或系統環境編碼
    //     } elseif ($target_encoding == $ENCODING_UTF8) {
    //         $message = mb_convert_encoding($message, "utf-8", $message_encodeFrom);
    //     } else {
    //         $message = iconv($message_encodeFrom,"ucs-2",$message);
    //         $message = mb_convert_encoding($message, "ucs-2", $message_encodeFrom);
    //     }

    //     /*建立連線*/
    //     $mysms = new sms2($target_encoding);
    //     $ret_code = $mysms->create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd);
    //     $ret_msg = $mysms->get_ret_msg();

    //     if ($ret_code == 0) {
    //         //如欲傳送多筆簡訊，連線成功後使用迴圈執行$mysms->send_text()即可
    //         //send_text(門號, 型態:[1=立即, 2=立即+重送逾時, 3=預約, 4=預約+重送逾時], 預約時間, 重送逾時, 簡訊內容)
    //         $ret_code = $mysms->send_text($mobile_number, 1, "", 0, $message);
    //         // $ret_code = $mysms->send_text($mobile_number, 2 , "" , 1440 ,$message);
    //         // $ret_code = $mysms->send_text($mobile_number, 3 , "200903081500" , 0 ,$message); //yyMMddHHmmss
    //         // $ret_code = $mysms->send_text($mobile_number, 4 , "200903081500" , 1440 ,$message);
    //         $ret_msg = $mysms->get_ret_msg();
    //         $result = array(
    //             "status" => 1,
    //             "connect"=> "連線成功",
    //             "msg"=> $ret_msg,
    //         );
    //     } else {
    //         $result = array(
    //             "status" => 0,
    //             "connect"=> "連線失敗",
    //             "msg"=> $ret_msg,
    //         );
    //     }
    //     /*關閉連線*/
    //     $mysms->close_conn();

    //     return $result;
    // }
}
