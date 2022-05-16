<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Mgt_users_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("Mgt_users_service");
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

    // 修改密碼 by userId
    public function update_password_post(){   
        $data = array(
            "password_old" => $this->security->xss_clean($this->input->post("password_old")),
            "password_new" => $this->security->xss_clean($this->input->post("password_new")),
            "check_password" => $this->security->xss_clean($this->input->post("check_password"))
        );
        $this->form_validation->set_rules("password_old", "lang:「舊密碼」", "trim|required");
        $this->form_validation->set_rules("password_new", "lang:「新密碼」","trim|required|min_length[5]|max_length[12]|alpha_numeric");
        $this->form_validation->set_rules("check_password", "lang:「確認密碼」","trim|required|min_length[5]|max_length[12]|alpha_numeric|matches[password_new]");

        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->mgt_users_service->update_password($data),200); // REST_Controller::HTTP_OK
        }
    }

    // 取得產業類別
    public function get_industry_post(){   
        $this->response( $this->mgt_users_service->get_industry(),200); // REST_Controller::HTTP_OK     
    } 

    // 修改使用者帳號狀態(凍結/解凍) by userId
    public function update_isDeleted_by_id_post(){   
        $data = array(
            "userId" => $this->security->xss_clean($this->input->post("userId")),
            "isDeleted" => $this->security->xss_clean($this->input->post("isDeleted"))
        );
        $this->form_validation->set_rules("userId", "lang:「使用者ID」", "trim|required");
        $this->form_validation->set_rules("isDeleted", "lang:「帳號狀態」","trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->mgt_users_service->update_isDeleted_by_id($data),200); // REST_Controller::HTTP_OK
        }
    }

    // 使用者名片查詢
    public function query_users_post(){   
        $data = array(
            "account" => $this->security->xss_clean($this->input->post("account")),
            "superID" => $this->security->xss_clean($this->input->post("superID")),
            "company" => $this->security->xss_clean($this->input->post("company")),
            "industryId" => $this->security->xss_clean($this->input->post("industryId")),
            "startDT" => $this->security->xss_clean($this->input->post("startDT")),
            "endDT" => $this->security->xss_clean($this->input->post("endDT")),
            "page" => $this->security->xss_clean($this->input->post("page")),
            "page_count" => $this->security->xss_clean($this->input->post("page_count")),
        );
        $this->form_validation->set_rules("page", "page", "trim|required|numeric");
        $this->form_validation->set_rules("page_count", "page_count", "trim|required|numeric");
        if(!empty($data['startDT']) || !empty($data['endDT'])){
            $this->form_validation->set_rules("startDT", "lang:「起始時間」", "trim|required|callback_timestamp_validation");
            $this->form_validation->set_rules("endDT", "lang:「結束時間」", "trim|required|callback_timestamp_validation");
        }
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }
        $this->response($this->mgt_users_service->query_users($data),200); // REST_Controller::HTTP_OK
    }
}