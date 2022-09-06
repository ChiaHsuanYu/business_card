<?php
require APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_msg extends BaseController {

    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url'); 
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->service("Common_service");
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        
    }
    // 現階段不需使用此功能
    public function index($token){
        //登入驗證
        $r = $this->checkAA_front($token);
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            exit("Invalid Token");
        }
        $count=0;
        // 讓迴圈無限執行
        while (true) {
            // 取得通知訊息(系統通知or收藏要求)
            $data = $this->common_service->check_notify();
            // 將資料編碼 json 傳送
            echo "data: ".json_encode($data);
            echo "\n\n";
            flush();
            ob_flush();

            // 控制睡眠多久再執行（秒）
            sleep(1);
            $count++; 
            if($count>10){
                echo "結束";
                exit;
            }
        }
    }
}