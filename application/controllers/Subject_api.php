<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Subject_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Subject_service");
        $this->load->library('session');

        // 登入驗證
        $r = $this->checkAA_front();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            $this->response($r,401); // REST_Controller::HTTP_OK     
            exit("Invalid Token");
        }
    }

    // 取得主題清單
    public function query_all_post(){   
        $this->response( $this->subject_service->query_all(),200); // REST_Controller::HTTP_OK     
    } 
}