<?php
class Subject_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('subject_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }
   
    // 取得主題清單
    public function query_all(){
        $identity = $this->session->user_info['identity'];
        $r = $this->subject_model->query_all($identity);
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