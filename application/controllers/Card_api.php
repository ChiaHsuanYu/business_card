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

    //更新名片收藏狀態 by id
    public function update_isCollected_by_id_post(){
        $data = array(
            "collectId" => $this->security->xss_clean($this->input->post("collectId")),
            "isCollected" => $this->security->xss_clean($this->input->post("isCollected")),
        );
        $this->form_validation->set_rules('collectId', 'lang:「收藏紀錄ID」', 'required');
        $this->form_validation->set_rules('isCollected', 'lang:「收藏狀態」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->card_service->update_isCollected_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //取得收藏要求清單
    public function get_collect_by_userId_post(){
        $this->response($this->card_service->get_collect_by_userId(),200); // REST_Controller::HTTP_OK     
    }
    
    //查詢使用者ID
    public function query_users_id_post(){
        $data = array(
            "areaId" => $this->security->xss_clean($this->input->post("areaId")),
            "industryId" => $this->security->xss_clean($this->input->post("industryId")),
        );
        $this->response($this->card_service->query_users_id($data),200); // REST_Controller::HTTP_OK     
    }

    //取得隨機名片列表
    public function query_user_post(){
        $data = array(
            "start_index" => $this->security->xss_clean($this->input->post("start_index")),
            "length" => $this->security->xss_clean($this->input->post("length")),
        );
        $this->form_validation->set_rules('start_index', 'lang:「開始位置」', 'required');
        $this->form_validation->set_rules('length', 'lang:「數量」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->card_service->query_user($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //取得收藏名片列表
    public function query_user_collect_post(){
        $data = array(
            "areaId" => $this->security->xss_clean($this->input->post("areaId")),
            "industryId" => $this->security->xss_clean($this->input->post("industryId")),
        );
        $this->response($this->card_service->query_user_collect($data),200); // REST_Controller::HTTP_OK     
    }

    //取得被收藏的使用者清單(查看被哪些人收藏的功能)
    public function get_user_for_collected_post(){
        $this->response($this->card_service->get_user_for_collected(),200); // REST_Controller::HTTP_OK     
    }

    //新增瀏覽紀錄
    public function add_scan_record_post(){
        $data = array(
            "scan_userId" => $this->security->xss_clean($this->input->post("scan_userId")),
            "scanTime" => $this->security->xss_clean($this->input->post("scanTime")),
        );
        $this->form_validation->set_rules('scan_userId', 'lang:「使用者ID」', 'required');
        $this->form_validation->set_rules('scanTime', 'lang:「瀏覽時間」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->card_service->add_scan_record($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //取得瀏覽紀錄
    public function query_scan_record_post(){
        $data = array(
            "areaId" => $this->security->xss_clean($this->input->post("areaId")),
            "industryId" => $this->security->xss_clean($this->input->post("industryId")),
        );
        $this->response($this->card_service->query_scan_record($data),200); // REST_Controller::HTTP_OK     
    }
}