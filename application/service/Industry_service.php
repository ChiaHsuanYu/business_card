<?php
class Industry_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('industry_model');
        $this->load->service('common_service');
        $this->load->library('session');
    }
   
    // 取得產業類別
    public function query_all(){
        $r = $this->industry_model->query_all();
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