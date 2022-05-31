<?php
class Mgt_subject_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('subject_model');
        $this->load->model('template_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }

    // 取得主題元件清單
    public function query_template(){
        $r = $this->template_model->query_all();
        if($r){
            $result = array(
                "status" => 1,
                "data"=> $r
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "查無元件"
            );    
        }
        return $result;
    }

    // 取得主題 by subjectId
    public function get_subject($data){
        $r = $this->subject_model->get_subject($data);
        if($r){
            $result = array(
                "status" => 1,
                "data"=> $r
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "查無主題"
            );    
        }
        return $result;
    }

    // 新增主題
    public function add_subject($data){
        $this->common_service->logger("add_subject");
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

    // 修改主題
    public function edit_subject($data){
        $this->common_service->logger("edit_subject");
        $r = $this->subject_model->update_subject_by_id($data);
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

    // 取得主題清單
    public function query_all(){
        $r = $this->subject_model->query_all();
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

    // 發布主題 by subjectId
    public function update_isReleased_by_id($data){
        $this->common_service->logger("subjectId:".$data['subjectId'].",isReleased:1");
        $r = $this->subject_model->update_isReleased_by_id($data);
        if($r){
            $result = array(
                "status" => 1,
                "msg"=> "發布成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "發布失敗"
            );    
        }
        return $result;
    }

    // 刪除主題 by subjectId
    public function update_isDeleted_by_id($data){
        $this->common_service->logger("subjectId:".$data['subjectId'].",isDeleted:1");
        $r = $this->subject_model->update_isDeleted_by_id($data);
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