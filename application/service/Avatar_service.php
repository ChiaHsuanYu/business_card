<?php
class Avatar_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('avatar_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }
   
    // 取得系統預設頭像清單
    public function query_all(){
        $r = $this->avatar_model->query_all();
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