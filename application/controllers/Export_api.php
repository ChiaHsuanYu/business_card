<?php

require_once APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        // $this->load->library(array("form_validation"));
        // $this->load->helper('download');
        $this->load->helper("security");
        $this->load->service("Export_service");
        $this->load->library('session');
    }

    // 匯出Vcf檔
    public function vcard_post(){   
        $data = array(
            "companyId" => $this->security->xss_clean($this->input->post("companyId")),
        );
        $this->form_validation->set_rules('companyId', 'lang:「公司ID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->export_service->vcard($data),200); // REST_Controller::HTTP_OK     
        }
    } 
}