<?php
class Mgt_template_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('template_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }

    // 取得模板 by templateId
    public function get_template($data){
        $r = $this->template_model->get_template($data);
        if($r){
            $result = array(
                "status" => 1,
                "data"=> $r
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "查無模板"
            );    
        }
        return $result;
    }

    // 新增模板
    public function add_template($data){
        $this->common_service->logger("add_template");
        $r = $this->template_model->add_template($data);
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

    // 修改模板
    public function edit_template($data){
        $this->common_service->logger("edit_template");
        $r = $this->template_model->update_template_by_id($data);
        if($r){
            $result = array(
                "status" => 1,
                "msg"=> "修改成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );    
        }
        return $result;
    }

    // 取得模板清單
    public function query_all(){
        $r = $this->template_model->query_all();
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

    // 刪除模板 by templateId
    public function update_isDeleted_by_id($data){
        $this->common_service->logger("templateId:".$data['id'].",isDeleted:1");
        $r = $this->template_model->update_isDeleted_by_id($data);
        if($r){
            $result = array(
                "status" => 1,
                "msg"=> "刪除成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "刪除失敗"
            );    
        }
        return $result;
    }
}