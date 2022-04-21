<?php
class Mgt_subject_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('subject_model');
        $this->load->service('common_service');
        $this->load->library('session');
    }

    // 新增主題
    public function add_subject($data){
        $r = $this->subject_model->add_subject($data);
        if($r){
            $result = array(
                "status" => 1,
                "msg"=> "新增成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "新增失敗"
            );    
        }
        return $result;
    }
}