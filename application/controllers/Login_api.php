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
        $this->load->library('facebook'); 
    }

    // 檢查是否已有登入紀錄
    public function check_login_post(){   
        $result = $this->checkAA_front();
        if ($result['status'] == 1) {
            if($result['data']->isDeleted){
                $result = array(
                    "status" => 0,
                    "msg"=> "手機號碼已被凍結"
                );    
            }else{
                $this->session->user_info = (array)$result['data'];   
                // 整理資料-依照順序取得公司資訊 by companyId,userId
                $userInfo = $this->login_service->get_company($result['data']);
                // 檢查基本是否為空
                $isBasicInfoEmpty = true;
                if(!empty($result['data']->personal_superID)){
                    $isBasicInfoEmpty = false;
                }
                $result = array(
                    "status" => 2,
                    "msg" => "已有登入紀錄，直接導向主頁",
                    "isBasicInfoEmpty" => $isBasicInfoEmpty,
                    "data" => $userInfo,
                ); 
            }
        }else{
            $result = array(
                "status" => 1,
                "msg"=> "尚無登入紀錄"
            );  
        }
        $this->response($result,200); // REST_Controller::HTTP_OK 
    } 

    // 登入
    public function login_post(){   
        $account = $this->security->xss_clean($this->input->post("account"));
        $this->form_validation->set_rules('account', 'lang:「手機號碼」', 'required|callback_phone_validation');
        // numeric|regex_match[/^09([0-9]{8})$/]

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $result = $this->login_service->login($account);
            $this->response($result,200); // REST_Controller::HTTP_OK     
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
            $result = $this->login_service->account_verify($data);
            if ($result['status'] == 1) {                
                // 限制設備裝置登入數量
                $device = $this->get_device_type();
                $this->common_service->restrict_user_device($result['data'][0]->id,$device);
                //更新Token, createDT, updateDT
                $new_Token = $this->renewToken_front($result['data'][0]->id,$result['data'][0]->account);
                $result = $this->common_service->checkToken_front($new_Token['Token']);//重新取得使用者資訊
                //驗證成功，紀錄帳號資料到SESSION
                $this->session->user_info = (array)$result['data'][0];
                // 整理資料-依照順序取得公司資訊 by companyId,userId
                $userInfo = $this->login_service->get_company($result['data'][0]);
                // 檢查基本是否為空
                $isBasicInfoEmpty = true;
                if(!empty($result['data'][0]->personal_superID)){
                    $isBasicInfoEmpty = false;
                }
                $result = array(
                    "status" => 1,
                    "msg" => '驗證成功',
                    "isBasicInfoEmpty" => $isBasicInfoEmpty,
                    "data" => $userInfo,
                ); 
            }
            $this->response($result,200); // REST_Controller::HTTP_OK     
        }
    } 

    // 登出
    public function logout_post(){
        $google_client = new Google_Client();
        $google_client->revokeToken();

        if($this->session->user_info){
            //清除token
            $this->deleteToken_front($this->session->user_info['token']);
        }
        
        //清除session
        if(isset($this->session->user_info)){
            $this->session->sess_destroy();
        }

        if($this->session->userdata('access_token')){
            $this->session->unset_userdata('access_token');
        }
        $result = array(
            "status" => 0,
            "msg" => $this->session->user_info,
        ); 
        $this->response($result,200); // REST_Controller::HTTP_OK
    }

    // 第三方登入驗證
    public function Social_login_post(){   
        $social_type = $this->security->xss_clean($this->input->post("social_type"));
        $access_token = $this->security->xss_clean($this->input->post("access_token"));
        $this->form_validation->set_rules('social_type', 'social_type', 'required');
        $this->form_validation->set_rules('access_token', 'access_token', 'required');

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $result = $this->login_service->get_social_login_data($social_type,$access_token);
            if($result['status']){
                // 限制設備裝置登入數量
                $device = $this->get_device_type();
                $this->common_service->restrict_user_device($result['data'][0]->id,$device);
                //更新Token, createDT, updateDT
                $new_Token = $this->renewToken_front($result['data'][0]->id,$result['data'][0]->google_uid);
                $result = $this->common_service->checkToken_front($new_Token['Token']);//重新取得使用者資訊
                //驗證成功，紀錄帳號資料到SESSION
                $this->session->user_info = (array)$result['data'][0];
                // 整理資料-依照順序取得公司資訊 by companyId,userId
                $userInfo = $this->login_service->get_company($result['data'][0]);
                // 檢查基本是否為空
                $isBasicInfoEmpty = true;
                if(!empty($result['data'][0]->personal_superID)){
                    $isBasicInfoEmpty = false;
                }
                $result = array(
                    "status" => 1,
                    "msg" => '驗證成功',
                    "isBasicInfoEmpty" => $isBasicInfoEmpty,
                    "data" => $userInfo,
                ); 
            }
            $this->response($result,200); // REST_Controller::HTTP_OK     
        }
    } 
}