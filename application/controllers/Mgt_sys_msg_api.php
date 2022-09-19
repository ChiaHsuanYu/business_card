<?php

require_once APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Mgt_sys_msg_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("Sys_msg_service");
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

    //新增系統通知訊息
    public function add_sys_msg_post(){
        $data = array(
            "title" => $this->security->xss_clean($this->input->post("title")),
            "msg" => $this->security->xss_clean($this->input->post("msg")),
        );
        $this->form_validation->set_rules('title', 'lang:「系統通知標題」', 'required');
        $this->form_validation->set_rules('msg', 'lang:「系統通知訊息」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->sys_msg_service->add_sys_msg($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //修改系統通知訊息
    public function update_sys_msg_by_id_post(){
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
            "title" => $this->security->xss_clean($this->input->post("title")),
            "msg" => $this->security->xss_clean($this->input->post("msg")),
        );
        $this->form_validation->set_rules('id', 'lang:「系統訊息ID」', 'required');
        $this->form_validation->set_rules('title', 'lang:「系統通知標題」', 'required');
        $this->form_validation->set_rules('msg', 'lang:「系統通知訊息」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->sys_msg_service->update_sys_msg_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //刪除系統通知訊息
    public function delete_sys_msg_by_id_post(){
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
        );
        $this->form_validation->set_rules('id', 'lang:「系統訊息ID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->sys_msg_service->delete_sys_msg_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //取得系統通知訊息
    public function get_sys_msg_post(){
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
        );
        $this->form_validation->set_rules('id', 'lang:「通知訊息ID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->sys_msg_service->get_sys_msg($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //系統通知訊息列表
    public function query_sys_msg_post(){
        $data = array(
            "page" => $this->security->xss_clean($this->input->post("page")),
            "page_count" => $this->security->xss_clean($this->input->post("page_count")),
            "title" => $this->security->xss_clean($this->input->post("title")),
            "msg" => $this->security->xss_clean($this->input->post("msg")),
        );
        $this->form_validation->set_rules("page", "page", "trim|required|numeric");
        $this->form_validation->set_rules("page_count", "page_count", "trim|required|numeric");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }
        $this->response($this->sys_msg_service->query_sys_msg($data),200); // REST_Controller::HTTP_OK     
    }
}