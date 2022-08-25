<?php
class Contact_setting_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Common_service");
        $this->load->model('contact_setting_model');
        $this->load->driver('cache');
    }
    // 取得親密度累積設定
    public function get_contact_setting(){
        $r = $this->contact_setting_model->get_contact_setting();
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );  
            return $result;
        }
        $result = array(
            "status" => 1,
            "data"=> $r
        );
        return $result;
    }

    // 更新親密度累積設定
    public function update_contact_setting_by_id($data){
        $r = $this->contact_setting_model->update_contact_setting_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "設定更新失敗"
            );    
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> "設定更新成功"
        );  
        return $result;
    }
}