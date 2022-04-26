<?php

/* 名稱：hiAir SMS2 For PHP的Class
 * 撰寫者 : HiNet - hiAir , Chih-Ming Liao
 * 日期 : 2006/06/27
 * 修改者 : HiNet - hiAir , Mike
 * 修改日期 : 2020/09/24
 * 備註 : 
 */

class sms2{
   
   public function __construct(){}

   public $usenet_handle;    /* socket handle*/
   public $ret_code;
   public $ret_msg;
   public $send_msisdn="";
   public $send_msg_len=266; /* Socket 傳送 SendMsg 的長度為266 */
   public $ret_msg_len=244;  /* Socket 接收 RetMsg 的長度為244 */
   public $send_set_len=100;
   public $send_content_len=160;
   public $ENCODING_BIG5=1;
   public $ENCODING_UCS2=3;
   public $ENCODING_UTF8=4;
   public $encoding_num= 1; #1:BIG5, 3:UCS-2, 4:UTF-8
   //function sms2(){}
   function sms2($encoding_num = null){ 
      if($encoding_num==$this->ENCODING_BIG5){
          $this->encoding_num = $this->ENCODING_BIG5;
      }elseif($encoding_num==$this->ENCODING_UTF8) {
          $this->encoding_num = $this->ENCODING_UTF8;
      }else{
          $this->encoding_num = $this->ENCODING_UCS2;
      }
   }

   /* 函式說明：建立連線與認證
    * $server_ip:伺服器IP, $server_port:伺服器Port, $TimeOut:連線timeout時間
    * $user_acc:帳號, $user_pwd:密碼
    * return -1：網路連線失敗, 0：連線與認證成功, 1:連線成功，認證失敗
    */
   function create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd){
      $msg_type=0;	   /* 0:檢查帳號密碼 1:傳送簡訊 2:查詢傳送結果 */

      $this->usenet_handle = fsockopen($server_ip, $server_port, $errno, $errstr, $TimeOut);
      if(!$this->usenet_handle) {
      	 $this->ret_code=-1;
      	 $this->ret_msg="Connection failed!";
      	 return $this->ret_code;
      }
      /* 帳號密碼檢查 */
      $msg_set=$user_acc . "\0" . $user_pwd . "\0";
      $in_temp = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      /* 取出ret_code */
      $ret_C = substr($out, 0, 1);           /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
      $ret_code_value = $ret_code_array[1];    /* array[1]為ret_code的值 */
      /* 取出ret_content*/
      $ret_CL = substr($out, 3, 1);          /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL);  /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/

      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }   

   /* 函式說明：傳送文字簡訊
    * $tel:接收門號, 簡訊內容
    * return ret_code
    */
   // function send_text($mobile_number, $message){
   function send_text($mobile_number, $send_transType = 1, $send_orderTime = "", $send_limitTime = 1440, $message){
   	if(substr($mobile_number, 0, 1)== "+"){
      	 $msg_type=15; /* 傳送國際簡訊 */
      }else{
      	 $msg_type=1; /* 傳送國內簡訊 */
      }

      //建構msg_set
      $msg_set_str = $mobile_number . "\0" . sprintf("%02d", $send_transType) . "\0";
      if($send_transType == 1){
         //立即
      }else if( $send_transType == 2){
         //立即+重送逾時
         $msg_set_str = $msg_set_str . sprintf("%04d", $send_limitTime) . "\0";
      }else if( $send_transType == 3){
         //預約
         $msg_set_str = $msg_set_str . $send_orderTime . "\0";
      }else if( $send_transType == 4){
         //預約+重送逾時
         $msg_set_str = $msg_set_str . $send_orderTime . "\0" . sprintf("%04d", $send_limitTime) . "\0";
      }

      /*---將未滿$msg_set長度的資料填\0補滿 */
      $len_p = $this->send_set_len - strlen($msg_set_str);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      $msg_set = $msg_set_str . $zero_buf;
   

      /*---將未滿$msg_content長度的資料填\0補滿 */
      $len_p = $this->send_content_len - strlen($message);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      $msg_content = $message . $zero_buf;
         
      $in = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set_str)) . pack("C",strlen($message)) . $msg_set . $msg_content;
      
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
      $ret_code_value = $ret_code_array[1];    /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/

      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }

	/*發送長簡訊
	*/
	function send_long($mobile_number, $send_transType = 1 , $send_orderTime, $send_limitTime = 1440, $message, $encodeFrom="UTF-8"){
      //長簡訊每則字數限制, ASCII:153, UCS2:67
      $msg_size_limit = 0;

      //字組長度
      $words_length = mb_strlen($message, $encodeFrom);
      //測試UTF-8長度(位元組長度)
      //mb_convert_encoding(message,encodeTo,encodeFrom), encodeTo:使用哪種編碼傳送, encodeFrom:簡訊內容編碼或系統環境編碼
      $utf8_bytes = mb_convert_encoding($message,"UTF-8",$encodeFrom);
      $utf8_bytes_len = strlen($utf8_bytes);
      // echo "words_length(strlen): ".$words_length."<br>\n";
      // echo "utf8_bytes_len(strlen): ".$utf8_bytes_len."<br>\n";

      //如果兩者相同，表示純英數
      if($utf8_bytes_len == $words_length){
			//如果utf8 bytes長度跟原始內容長度相等，為純英數字簡訊
			//純英數字，須以BIG5傳送，才能每則153
         $msg_size_limit = 153;
         $coding = 1;
         $encoding = "BIG5";
         // echo "純英數字，以BIG5送出。<br>\n";
      }else{
         //非純英數字，須以UCS2傳送，才能每則67
         $msg_size_limit = 67;
         $coding = 3;
         $encoding = "UCS-2BE";
         // echo "非純英數字，以UCS2送出。<br>\n";
      }
      //範例:
      // $string="天氣很好";
      // echo "byte_length(strlen): ".strlen($string)."<br>\n";
      // echo "words_length(mb_strlen): ".mb_strlen( $string, "utf-8")."<br>\n";
      //結果:
      // byte_length(strlen): 12<br>
      // words_length(mb_strlen): 4<br>

      //編碼轉換
      // $message = iconv($encodeFrom, $encoding,$message);
      $message = mb_convert_encoding($message,$encoding,$encodeFrom);
      $message_len = mb_strlen($message, $encoding);

		
		//計算需要發送多少通分則
		$numOfMsg = ceil($message_len / $msg_size_limit);
		// echo "numOfMsg:".$numOfMsg."<BR>\n";
				 
		$in = "";
		
		for($cnt =0 ; $cnt <= $numOfMsg; $cnt++){
		
			if($cnt == 0){
				//第一通，傳送長簡訊資訊
				if(substr($mobile_number, 0, 1) == "+"){
					$msg_type = 18;
				}else{
					$msg_type = 11;	
				}
				
				$msg_coding = $coding;
				$msg_priority = 1;
				$msg_country_code = 0;
				
				//建構msg_set
				$msg_set_str = $mobile_number . "\0" . sprintf("%02d", $send_transType) . "\0";
				if($send_transType == 1){
					//立即
				}else if( $send_transType == 2){
					//立即+重送逾時
					$msg_set_str = $msg_set_str . sprintf("%04d", $send_limitTime) . "\0";
				}else if( $send_transType == 3){
					//預約
					$msg_set_str = $msg_set_str . $send_orderTime . "\0";
				}else if( $send_transType == 4){
					//預約+重送逾時
					$msg_set_str = $msg_set_str . $send_orderTime . "\0" . sprintf("%04d", $send_limitTime) . "\0";
				}
				
				//建構msg_set並補\0
				$msg_len = strlen($msg_set_str);
				$len_p = $this->send_set_len - strlen($msg_set_str);
				$zero_buf = "";
				for($i = 0 ; $i<$len_p ; $i++){
					$zero_buf = $zero_buf . "\0";
				}
				$msg_set = $msg_set_str . $zero_buf;
				
				//建構msg_content,用不到，直接清空
				$msg_content = "\0";
				$msg_content_len = 0;
				$len_p = $this->send_content_len - strlen($msg_content);
				$zero_buf ="";
				for($i = 0 ; $i<$len_p ; $i++){
					$zero_buf = $zero_buf . "\0";
				}
				$msg_content =  $msg_content . $zero_buf;
				
				$in = pack("C", $msg_type).pack("C", $msg_coding).pack("C",$msg_priority).pack("C", $msg_country_code).pack("C", strlen($msg_set)).pack("C",$numOfMsg).$msg_set.$msg_content;
			}else{
				//第二通以後，傳送長簡訊內容
				$msg_type = $cnt;	//填第幾通，從1開始
				$msg_coding = $coding; //編碼
				$msg_priority = 1; //用不到
				$msg_country_code = 0; //用不到
				$msg_set_len = 0;
				
				//建構msg_set，用不到，直接補'\0'
				$msg_set_str = "\0";
				$msg_len = strlen($msg_set_str);
				$len_p = $this->send_set_len - strlen($msg_set_str);
				$zero_buf = "";
				for($i = 0 ; $i<$len_p ; $i++){
					$zero_buf = $zero_buf . "\0";
				}
				$msg_set = $msg_set_str . $zero_buf;
				
				
				//建構msg_content
            $msg_part_bytes = mb_substr($message, ( ($cnt-1) *$msg_size_limit), $msg_size_limit, $encoding);
            // echo "msg_part_bytes: ".$msg_part_bytes."<br>\n";
            
				$msg_content_len = strlen($msg_part_bytes);
            // echo "msg_content_len: ".$msg_content_len."<br>\n";
            $len_p = $this->send_content_len - $msg_content_len;
				$zero_buf = "";
				for($i = 0; $i<$len_p ; $i++){
					$zero_buf = $zero_buf . "\0";
				}
				$msg_content = $msg_part_bytes . $zero_buf;
				
				$in = pack("C", $msg_type) . pack("C", $msg_coding). pack("C", $msg_priority).pack("C", $msg_country_code).pack("C",$msg_set_len).pack("C",$msg_content_len).$msg_set.$msg_content;
			}
			
			$write = fwrite($this->usenet_handle, $in);
			$out = fread($this->usenet_handle, $this->ret_msg_len);
			$ret_C = substr($out, 0, 1); //取出ret_code
			$ret_code_array = unpack("C", $ret_C);
         $ret_code_value = $ret_code_array[1];    /* array[1]為ret_code的值 */
			
			$ret_CL = substr($out, 3,1 );
			$ret_cl_array = unpack("C", $ret_CL);
			$ret_content_len = $ret_cl_array[1];
			$ret_content = substr($out, 84, $ret_content_len);
			
			$this->ret_code = $ret_code_value;
			$this->ret_msg = $ret_content;
			
			//如果傳送中有任何一通return code不等於零，即離開
			if($this->ret_code != 0){
				return $this->ret_code;
			}
		}
		return $this->ret_code;
	}
   

   /* 函式說明：查詢text發訊結果
    * $messageid:訊息ID
    * return ret_code
    */
   function query_text($messageid){
      $msg_type=2; /* 查詢text傳送結果 */
      $msg_set=$messageid;
      $in_temp = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }
   
   /* 函式說明：查詢長簡訊發訊結果
    * $messageid:訊息ID
    * return ret_code
    */
   function query_long($messageid){
      $msg_type=12; /* 查詢text傳送結果 */
      $msg_set=$messageid;
      $in_temp = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }
   
   /* 函式說明：取消預約短簡訊
    * $messageid:訊息ID
    * return ret_code
    */
   function cancel_text($messageid){
      $msg_type=16; /* 取消預約文字簡訊 */
      $msg_set=$messageid;
      $in_temp = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }

   /* 函式說明：取消預約長簡訊
    * $messageid:訊息ID
    * return ret_code
    */
   function cancel_long($messageid){
      $msg_type=17; /* 取消預約文字(含國際)長簡訊 */
      $msg_set=$messageid;
      $in_temp = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   } 

   /* 回傳ret_content的值 */
   function get_ret_msg(){
      return $this->ret_msg;
   }

   /* 回傳send_tel的值 */
   function get_send_tel(){
      return $this->send_msisdn;
   }
  
   /* 關閉連線 */
   function close_conn(){
   	  if($this->usenet_handle)
         fclose($this->usenet_handle);
   }
}
?>
