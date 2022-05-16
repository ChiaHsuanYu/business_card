<?php
class Social_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('social_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }
   
    // 取得社群清單
    public function query_all(){
        $r = $this->social_model->query_all();
        if ($r){
            $result = array(
                "status" => 1,
                "data"=> $r
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