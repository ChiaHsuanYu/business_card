<?php

/* 名稱：hiAir Send Text For PHP範例程式
 * 撰寫者 : HiNet - hiAir , Chih-Ming Liao
 * 日期 : 2006/06/27
 */

include "sms2.inc";

error_reporting (E_ALL);

echo "<h2> hiAir 查詢文字簡訊傳送結果 </h2>\n";

/* Socket to Air Server IP ,Port */
$server_ip = '202.39.54.130';
$server_port = 8000;
$TimeOut=60;

$user_acc  = "89881560";
$user_pwd  = "qwe55664";
$messageid= "A2757956690460347097"; //從send_text.php 發送成功後取得如:A5972602463243283414 或 B7501892817456299327


/*建立連線*/
$mysms = new sms2();
$ret_code = $mysms->create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd);
$ret_msg = $mysms->get_ret_msg();

if($ret_code==0){ 
      echo "連線成功<br>\n";
      //長簡訊查詢用 query_long()、短簡訊查詢用 query_text()
      $ret_code = $mysms->query_text($messageid);
      // $ret_code = $mysms->query_long($messageid);
      $ret_msg = $mysms->get_ret_msg();
      echo "查詢結果:"."<br>\n";
      echo "ret_code=".$ret_code."<br>\n";
      echo "ret_msg=".$ret_msg."<br>\n";
} else {  
      echo "連線失敗"."<br>\n";
      echo "ret_code=".$ret_code."<br>\n";
      echo "ret_msg=".$ret_msg."<br>\n";
}

/*關閉連線*/
$mysms->close_conn();
?>

