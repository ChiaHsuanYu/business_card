<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Login_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("login_service");
        $this->load->library('session');
    }

    // 登入
    public function login_post(){   
        $cellphone = $this->security->xss_clean($this->input->post("cellphone"));
        $password = $this->security->xss_clean($this->input->post("password"));
        $this->form_validation->set_rules('cellphone', 'lang:「手機號碼」', 'required');
        $this->form_validation->set_rules('password', 'lang:「密碼」', 'required');

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $result = $this->login_service->login($cellphone,$password);
            if ($result['status'] == 1) {
                //更新Token, createDT, updateDT
                $new_Token = $this->renewToken($result['data'][0]->id,$result['data'][0]->cellphone);
                $result = $this->common_service->checkToken($new_Token['Token']);//重新取得使用者資訊

                //登入成功，紀錄帳號資料到SESSION
                $this->session->user_info = (array)$result['data'][0];
                $result = array(
                    "status" => 1,
                    "message" => "登入成功"
                ); 
            } else {
                $result = array(
                    "status" => 0,
                    "message" => "帳號密碼錯誤",
                );
            }
            $this->response( $result,200); // REST_Controller::HTTP_OK     
        }
    } 

    // 登出
    public function logout_post(){
        if($this->session->user_info){
            //清除token
            $this->deleteToken($this->session->user_info['token']);
        }
        //清除session
        if(isset($this->session->user_info)){
            $this->login_service->logout($this->session->user_info['cellphone']);
        }

        $result = array(
            "status" => 1,
            "message" => "登出成功"
        ); 
        $this->response($result,200); // REST_Controller::HTTP_OK
    }
}