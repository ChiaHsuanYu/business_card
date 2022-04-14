<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Users_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("users_service");
        $this->load->library('session');

        // 登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->user_info = (array)$r['data'][0];       
        }else{                              //Token不合法或逾時，讓使用者執行登出
            exit("Invalid Token");
        }
    }

    // 取得使用者資料
    // public function get_acc_post(){   
    //     $data = array(
    //         "id" => $this->security->xss_clean($this->input->post("id")),
    //     );
    //     $this->form_validation->set_rules('id', 'id', 'required');
    //     if ($this->form_validation->run() === FALSE) {
    //         $result = array(
    //             "status" => 0,
    //             "message" => $this->form_validation->error_string()
    //         ); 
    //         $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
    //     }else{
    //         $this->response( $this->users_service->get_acc($data),200); // REST_Controller::HTTP_OK     
    //     }
    // }

    // 修改基本資料
    // public function edit_personal_acc_post()
    // {   
    //     $data = array(
    //         "id" => $this->security->xss_clean($this->input->post("id")),
    //         "staffNum" => $this->security->xss_clean($this->input->post("staffNum")),
    //         "name" => $this->security->xss_clean($this->input->post("name")),
    //         "phone" => $this->security->xss_clean($this->input->post("phone")),
    //     );
    //     $this->form_validation->set_rules('id', 'id', 'required');
    //     $this->form_validation->set_rules('staffNum', 'lang:「人員編號」', 'required');
    //     $this->form_validation->set_rules('name', 'lang:「人員姓名」', 'required|max_length[20]');
    //     if($data['phone']){
    //         $this->form_validation->set_rules('phone', 'lang:「聯絡電話」', 'regex_match[/^[0-9\#\-]{9,50}$/]|max_length[50]');
    //     }
    //     //判斷規則是否成立
    //     if ($this->form_validation->run() === FALSE) {
    //         $result = array(
    //             "status" => 0,
    //             "message" => $this->form_validation->error_string()
    //         ); 
    //         $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
    //     }else{
    //         $this->response($this->users_service->edit_personal_acc($data),200); // REST_Controller::HTTP_OK     
    //     }
    // }

    // 修改密碼
    public function changePassword_post(){   
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
            "password_old" => $this->security->xss_clean($this->input->post("password_old")),
            "password_new" => $this->security->xss_clean($this->input->post("password_new"))
        );
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules("password", "lang:「密碼」", "trim|required|min_length[6]|max_length[12]");
        $this->form_validation->set_rules("check_password", "lang:「確認密碼」","trim|required|min_length[6]|max_length[12]|matches[password]");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->changePassword($data),200); // REST_Controller::HTTP_OK
        }
    }

    //修改帳號
    public function update_acc_by_id_post(){
        $data = array(
            "id" => $this->security->xss_clean($this->input->post("id")),
            "groupId" => $this->security->xss_clean($this->input->post("groupId")),
            "account" => $this->security->xss_clean($this->input->post("account")),
            "enable" => $this->security->xss_clean($this->input->post("enable")),
            "depId" => $this->security->xss_clean($this->input->post("depId")),
            "staffNum" => $this->security->xss_clean($this->input->post("staffNum")),
            "note" => $this->security->xss_clean($this->input->post("note"))
        );
        $this->form_validation->set_rules('groupId', 'lang:「群組」', 'required');
        $this->form_validation->set_rules('account', 'lang:「帳號」', 'required|max_length[20]');
        $this->form_validation->set_rules('enable', 'lang:「帳號狀態」', 'required');
        $this->form_validation->set_rules('depId', 'lang:「部門」', 'required|alpha_numeric');
        $this->form_validation->set_rules('staffNum', 'lang:「人員」', 'required|alpha_numeric');
        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->update_acc_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }
}