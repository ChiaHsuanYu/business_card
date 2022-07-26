<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Card_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("Card_service");
        $this->load->service('Common_service');
        $this->load->library('session');

        // 登入驗證
        $r = $this->checkAA_front();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            $this->response($r,401); // REST_Controller::HTTP_OK     
            exit("Invalid Token");
        }
    }

    // 收藏名片
    public function collect_user_by_userId_post(){   
        $data = array(
            "collect_userId" => $this->security->xss_clean($this->input->post("collect_userId")),
        );
        $this->form_validation->set_rules('collect_userId', 'lang:「收藏的使用者ID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->card_service->collect_user_by_userId($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //更改隱私設定 by userId
    public function update_isPublic_by_userId_post(){
        $data = array(
            "isPublic" => $this->security->xss_clean($this->input->post("isPublic")),
        );
        $this->form_validation->set_rules('isPublic', 'lang:「是否公開」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->update_isPublic_by_userId($data),200); // REST_Controller::HTTP_OK     
        }
    }
}