<?php

require_once APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Token_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->library('session');
    }

    // 檢查Token狀態
    public function check_token_post(){   
        $this->response( $this->checkAA(),200); // REST_Controller::HTTP_OK     
    }
}