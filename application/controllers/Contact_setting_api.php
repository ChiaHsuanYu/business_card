<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_setting_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("Contact_setting_service");
        $this->load->service('Common_service');
        $this->load->library('session');
        $this->load->helper('url');

        // 登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->mgt_user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            $this->response($r,401); // REST_Controller::HTTP_OK     
            exit("Invalid Token");
        }
    }
    //取得親密度累積設定
    public function get_contact_setting_post(){
        $this->response($this->contact_setting_service->get_contact_setting(),200); // REST_Controller::HTTP_OK     
    }

    //親密度累積設定
    public function update_contact_setting_by_id_post(){
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
            "distance" => $this->security->xss_clean($this->input->post("distance")),
            "max_contact_time" => $this->security->xss_clean($this->input->post("max_contact_time")),
            "min_contact_time" => $this->security->xss_clean($this->input->post("min_contact_time")),
        );
        $this->form_validation->set_rules('id', 'lang:「接觸條件設定ID」', 'required');
        $this->form_validation->set_rules('distance', 'lang:「最小距離」', 'required');
        $this->form_validation->set_rules('max_contact_time', 'lang:「最大接觸時間」', 'required');
        $this->form_validation->set_rules('min_contact_time', 'lang:「最小接觸時間」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->contact_setting_service->update_contact_setting_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }
}