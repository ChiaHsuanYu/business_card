<?php

require_once APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Country_code_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Country_code_service");
        $this->load->library('session');
    }

    // 取得各國國碼清單
    public function query_all_post(){   
        $this->response( $this->country_code_service->query_all(),200); // REST_Controller::HTTP_OK     
    } 
}