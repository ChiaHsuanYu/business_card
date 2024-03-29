<?php

/* 名稱：hiAir Send Text For PHP範例程式
 * 撰寫者 : HiNet - hiAir , Chih-Ming Liao
 * 撰寫日期 : 2006/06/27
 * 修改者 : HiNet - hiAir , Mike
 * 修改日期 : 2020/09/03
 * 備註 :
 * 重要提醒 : 如欲傳送多筆簡訊，連線成功後使用迴圈執行$mysms->send_text()即可
 */

include "sms2.inc";

error_reporting(E_ALL);

echo "<h2> hiAir 傳送文字簡訊 </h2>\n";

/* Socket to Air Server IP ,Port */
$server_ip = '202.39.54.130';
$server_port = 8000;
$TimeOut = 10;

$user_acc = "89881560";
$user_pwd = "qwe55664";
$mobile_number = "0912323062";
$message = "hiAir簡訊測試";

//static encoding number
$ENCODING_BIG5 = 1;
$ENCODING_UCS2 = 3;
$ENCODING_UTF8 = 4;

//簡訊內容編碼轉換
$message_encodeFrom = "utf-8"; //請確認簡訊內容編碼 "utf-8" or "big5" or "ucs-2" or other encodings
$target_encoding = $ENCODING_BIG5; //可選擇使用哪種編碼傳送
if ($target_encoding == $ENCODING_BIG5) {
    $message = mb_convert_encoding($message, "big5", $message_encodeFrom);
    //mb_convert_encoding(message,encodeTo,encodeFrom), encodeTo:使用哪種編碼傳送, encodeFrom:簡訊內容編碼或系統環境編碼
} elseif ($target_encoding == $ENCODING_UTF8) {
    $message = mb_convert_encoding($message, "utf-8", $message_encodeFrom);
} else {
    $message = mb_convert_encoding($message, "ucs-2", $message_encodeFrom);
}

/*建立連線*/
$mysms = new sms2($target_encoding);
$ret_code = $mysms->create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd);
$ret_msg = $mysms->get_ret_msg();

if ($ret_code == 0) {
      echo "連線成功<br>\n";
      //如欲傳送多筆簡訊，連線成功後使用迴圈執行$mysms->send_text()即可
      //send_text(門號, 型態:[1=立即, 2=立即+重送逾時, 3=預約, 4=預約+重送逾時], 預約時間, 重送逾時, 簡訊內容)
      $ret_code = $mysms->send_text($mobile_number, 1, "", 0, $message);
      // $ret_code = $mysms->send_text($mobile_number, 2 , "" , 1440 ,$message);
      // $ret_code = $mysms->send_text($mobile_number, 3 , "200903081500" , 0 ,$message); //yyMMddHHmmss
      // $ret_code = $mysms->send_text($mobile_number, 4 , "200903081500" , 1440 ,$message);
      $ret_msg = $mysms->get_ret_msg();
      if ($ret_code == 0) {
            echo "簡訊傳送成功<br>";
            echo "ret_code=" . $ret_code . "<br>\n";
            echo "ret_msg=" . $ret_msg . "<br>\n";
      } else {
            echo "簡訊傳送失敗" . "<br>\n";
            echo "ret_code=" . $ret_code . "<br>\n";
            echo "ret_msg=" . $ret_msg . "<br>\n";
      }
} else {
      echo "連線失敗" . "<br>\n";
      echo "ret_code=" . $ret_code . "<br>\n";
      echo "ret_msg=" . $ret_msg . "<br>\n";
}

/*關閉連線*/
$mysms->close_conn();
