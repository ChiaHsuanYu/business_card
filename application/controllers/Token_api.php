<?php

require APPPATH . 'controllers/BaseAPIController.php';
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
        $token = $this->security->xss_clean($this->input->post("token"));
        $this->form_validation->set_rules('token', 'token', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->checkAA($token),200); // REST_Controller::HTTP_OK     
        }
    }
}