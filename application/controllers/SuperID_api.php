<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class SuperID_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("users_service");
        $this->load->service('common_service');
        $this->load->library('session');

    }

    // 取得使用者資料 by superId,method:POST
    public function get_user_by_superId_post(){   
        $data = array(
            "superId" => $this->security->xss_clean($this->input->post("superId")),
        );
        $this->form_validation->set_rules('superId', 'lang:「superID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->users_service->get_user_by_superId($data),200); // REST_Controller::HTTP_OK     
        }
    }
}