<?php
class Country_code_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('country_code_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }
   
    // 取得國碼清單
    public function query_all(){
        $r = $this->country_code_model->query_all();
        if ($r){
            $result = array(
                "status" => 1,
                "data"=> $r,
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );    
        }
        return $result;
    }
}