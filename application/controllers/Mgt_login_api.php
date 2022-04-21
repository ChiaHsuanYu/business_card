<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Mgt_login_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct(){
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("mgt_login_service");
        $this->load->library('session');
    }

    // 檢查是否已有登入紀錄
    public function check_login_post(){   
        $this->response( $this->checkAA(),200); // REST_Controller::HTTP_OK      
    } 

    // 登入
    public function login_post(){   
        // session_unset();  //執行這個函式會清除所有SESSION
        $this->session->unset_userdata('mgt_user_info');
        $account = $this->security->xss_clean($this->input->post("account"));
        $password = $this->security->xss_clean($this->input->post("password"));
        $this->form_validation->set_rules('account', 'lang:「帳號」', 'required');
        $this->form_validation->set_rules('password', 'lang:「密碼」', 'required');
        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => "請輸入帳號密碼"
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $result = $this->mgt_login_service->login($account,$password);
            if ($result['status'] == 1) {                
                //更新Token, createDT, updateDT
                $new_Token = $this->renewToken($result['data'][0]->id,$result['data'][0]->account,'mgt');
                $result = $this->common_service->checkToken($new_Token['Token'],'mgt');//重新取得使用者資訊
                //驗證成功，紀錄帳號資料到SESSION
                $this->session->mgt_user_info = (array)$result['data'][0];
                $result = array(
                    "status" => 1,
                    "msg" => '登入成功',
                    "data" => $result['data'][0]
                ); 
            }
            $this->response($result,200); // REST_Controller::HTTP_OK     
        }
    } 

    // 登出
    public function logout_post(){
        if($this->session->mgt_user_info){
            //清除token
            $this->deleteToken($this->session->mgt_user_info['id'],'mgt');
        }
        //清除session
        if(isset($this->session->mgt_user_info)){
            $this->mgt_login_service->logout($this->session->mgt_user_info['account']);
        }
        $result = array(
            "status" => 1,
            "msg" => "登出成功"
        ); 
        $this->response($result,200); // REST_Controller::HTTP_OK
    }
}