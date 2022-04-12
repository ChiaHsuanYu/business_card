<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Industry_category_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("industry_category_service");
        $this->load->library('session');

        // 登入驗證
        // $r = $this->checkAA();
        // if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session            
        //     $this->session->user_info = (array)$r['data'][0];         
        // }else{                              //Token不合法或逾時，讓使用者執行登出
        //     exit("Invalid Token");
        // }
    }

    // 取得產業類別
    public function query_all_post(){   
        $this->response( $this->industry_category_service->query_all(),200); // REST_Controller::HTTP_OK     
    } 
}