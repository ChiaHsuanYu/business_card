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
        $account = $this->security->xss_clean($this->input->post("account"));
        $password = $this->security->xss_clean($this->input->post("password"));
        $this->form_validation->set_rules('account', 'lang:「手機號碼」', 'required');
        $this->form_validation->set_rules('password', 'lang:「密碼」', 'required');

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $result = $this->login_service->login($account,$password);
            if ($result['status'] == 1) {
                //更新Token, createDT, updateDT
                $new_Token = $this->renewToken($result['data'][0]->id,$result['data'][0]->account);
                $result = $this->common_service->checkToken($new_Token['Token']);//重新取得使用者資訊

                //登入成功，紀錄帳號資料到SESSION
                $this->session->user_info = (array)$result['data'][0];
                // 檢查基本是否為空
                $isBasicInfoEmpty = true;
                if(!empty($result['data'][0]->superID)){
                    $isBasicInfoEmpty = false;
                }
                $result = array(
                    "status" => 1,
                    "msg" => "登入成功",
                    "isBasicInfoEmpty" => $isBasicInfoEmpty
                ); 
            } else {
                $result = array(
                    "status" => 0,
                    "msg" => "帳號密碼錯誤",
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
            $this->login_service->logout($this->session->user_info['account']);
        }

        $result = array(
            "status" => 1,
            "msg" => "登出成功"
        ); 
        $this->response($result,200); // REST_Controller::HTTP_OK
    }
}