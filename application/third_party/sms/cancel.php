<?php

/* �W�١GhiAir Send Text For PHP�d�ҵ{��
 * ���g�� : HiNet - hiAir , Chih-Ming Liao
 * ��� : 2006/06/27
 */

include "sms2.inc";

error_reporting (E_ALL);

echo "<h2> hiAir �d�ߤ�r²�T�ǰe���G </h2>\n";

/* Socket to Air Server IP ,Port */
$server_ip = '202.39.54.130';
$server_port = 8000;
$TimeOut=60;

$user_acc  = "89881560";
$user_pwd  = "qwe55664";
$messageid= "A2757956690460347097";


/*�إ߳s�u*/
$mysms = new sms2();
$ret_code = $mysms->create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd);
$ret_msg = $mysms->get_ret_msg();

if($ret_code==0){ 
      echo "�d�ߵ��G:<br>\n";
      //��²�T������cancel_long(), �u²�T������ cancel_text()
      $ret_code = $mysms->cancel_long($messageid);
      // $ret_code = $mysms->cancel_text($messageid);
      $ret_msg = $mysms->get_ret_msg();
      echo "�d�ߵ��G:"."<br>\n";
      echo "ret_code=".$ret_code."<br>\n";
      echo "ret_msg=".$ret_msg."<br>\n";
} else {  
      echo "�s�u����"."<br>\n";
      echo "ret_code=".$ret_code."<br>\n";
      echo "ret_msg=".$ret_msg."<br>\n";
}

/*�����s�u*/
$mysms->close_conn();
?>
