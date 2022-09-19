<?php

require_once APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Mgt_template_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("Mgt_template_service");
        $this->load->service('Common_service');
        $this->load->library('session');

        // 登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->mgt_user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            $this->response($r,401); // REST_Controller::HTTP_OK     
            exit("Invalid Token");
        }
    }

    public function get_template_post(){
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
        );
        $this->form_validation->set_rules("id", "lang:「模板ID」", "trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->mgt_template_service->get_template($data),200); // REST_Controller::HTTP_OK
        }
    }

    // 新增模板
    public function add_template_post(){   
        $data = array(
            "template" => $this->security->xss_clean($this->input->post("template"))
        );
        $this->form_validation->set_rules("template", "lang:「模板名稱」","trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->mgt_template_service->add_template($data),200); // REST_Controller::HTTP_OK
        }
    }

    // 修改模板
    public function edit_template_post(){   
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
            "template" => $this->security->xss_clean($this->input->post("template"))
        );
        $this->form_validation->set_rules("id", "lang:「模板ID」","trim|required");
        $this->form_validation->set_rules("template", "lang:「模板名稱」","trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->mgt_template_service->edit_template($data),200); // REST_Controller::HTTP_OK
        }
    }

    // 取得模板清單
    public function query_all_post(){   
        $this->response( $this->mgt_template_service->query_all(),200); // REST_Controller::HTTP_OK     
    } 

    // 刪除模板 by templateId
    public function update_isDeleted_by_id_post(){   
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
        );
        $this->form_validation->set_rules("id", "lang:「模板ID」", "trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->mgt_template_service->update_isDeleted_by_id($data),200); // REST_Controller::HTTP_OK
        }
    }
}