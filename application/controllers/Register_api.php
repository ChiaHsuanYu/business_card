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
    // public function register_account_post(){   
    //     $account = $this->security->xss_clean($this->input->post("account"));
    //     $this->form_validation->set_rules('account', 'lang:「手機號碼」', 'required|numeric|regex_match[/^09([0-9]{8})$/]');
    //     if ($this->form_validation->run() === FALSE) {
    //         $result = array(
    //             "status" => 0,
    //             "msg" => $this->form_validation->error_string()
    //         ); 
    //         $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
    //     }else{
    //         $this->response( $this->register_service->register_account($account),200); // REST_Controller::HTTP_OK     
    //     }
    // } 

    // // 帳號驗證
    // public function account_verify_post(){   
    //     $data = array(
    //         'vaild' => $this->security->xss_clean($this->input->post("vaild")),
    //     );
    //     $this->form_validation->set_rules('vaild', 'lang:「驗證碼」', 'required');
    //     if ($this->form_validation->run() === FALSE) {
    //         $result = array(
    //             "status" => 0,
    //             "msg" => $this->form_validation->error_string()
    //         ); 
    //         $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
    //     }else{
    //         $result = $this->register_service->account_verify($data);
    //         if ($result['status'] == 1) {
    //             //更新Token, createDT, updateDT
    //             $new_Token = $this->renewToken($result['data'][0]->id,$result['data'][0]->account);
    //             $result = $this->common_service->checkToken($new_Token['Token']);//重新取得使用者資訊

    //             //驗證成功，紀錄帳號資料到SESSION
    //             $this->session->user_info = (array)$result['data'][0];

    //             //整理資料-依照順序取得公司資訊 by companyId,userId
    //             $user_data = $result['data'][0];
    //             $user_data->companyInfo = array();
    //             if($user_data->companyOrder){
    //                 for($i=0;$i<count($user_data->companyOrder);$i++){
    //                     $companyId = $user_data->companyOrder[$i];
    //                     $company_data = $this->company_model->get_company_by_userId($companyId,$user_data->id);
    //                     if(count($company_data)){
    //                         array_push($user_data->companyInfo,$company_data[0]);
    //                     }
    //                 }
    //             }
    //             $userInfo =  array(
    //                 'userInfo'=>$user_data
    //             );

    //             // 檢查基本是否為空
    //             $isBasicInfoEmpty = true;
    //             if(!empty($user_data->superID)){
    //                 $isBasicInfoEmpty = false;
    //             }
    //             $result = array(
    //                 "status" => 1,
    //                 "msg" => "驗證成功",
    //                 "isBasicInfoEmpty" => $isBasicInfoEmpty,
    //                 "data" => $userInfo
    //             ); 
    //         }
    //         $this->response($result,200); // REST_Controller::HTTP_OK     
    //     }
    // } 

    // // 設定密碼
    // public function add_password_post(){   
    //     $data = array(
    //         'password' => $this->security->xss_clean($this->input->post("password")),
    //     );
    //     $this->form_validation->set_rules("password", "lang:「密碼」", "trim|required|min_length[6]|max_length[20]|callback_check_string_validation");
    //     if ($this->form_validation->run() === FALSE) {
    //         $result = array(
    //             "status" => 0,
    //             "msg" => $this->form_validation->error_string()
    //         ); 
    //         $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
    //     }else{
    //         $this->response( $this->register_service->add_password($data),200); // REST_Controller::HTTP_OK     
    //     }
    // } 
}