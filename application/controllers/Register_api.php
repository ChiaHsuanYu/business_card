<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Register_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("register_service");
        $this->load->library('session');

        // 登入驗證
        // $r = $this->checkAA();
        // if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session            
        //     $this->session->user_info = (array)$r['data'][0];         
        // }else{                              //Token不合法或逾時，讓使用者執行登出
        //     exit("Invalid Token");
        // }
    }

    // 註冊帳號並發送簡訊驗證
    public function register_account_post(){   
        $account = $this->security->xss_clean($this->input->post("account"));
        $this->form_validation->set_rules('account', 'lang:「手機號碼」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->register_service->register_account($account),200); // REST_Controller::HTTP_OK     
        }
    } 

    // 帳號驗證
    public function account_verify_post(){   
        $data = array(
            'vaild' => $this->security->xss_clean($this->input->post("vaild")),
        );
        $this->form_validation->set_rules('vaild', 'lang:「驗證碼」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->register_service->account_verify($data),200); // REST_Controller::HTTP_OK     
        }
    } 

    // 設定密碼
    public function add_password_post(){   
        $data = array(
            'password' => $this->security->xss_clean($this->input->post("password")),
        );
        $this->form_validation->set_rules("password", "lang:「密碼」", "trim|required|min_length[6]|max_length[20]|callback_check_string_validation");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            // $id = $this->session->user_info['id'];
            $this->response( $this->register_service->add_password($data),200); // REST_Controller::HTTP_OK     
        }
    } 
}