<?php
class Card_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('user_collect_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }
    // 修改密碼
    public function update_password($data){
        // 檢查使用者舊密碼
        $data['id'] = $this->session->user_info['id'];
        $r = $this->users_model->check_user_by_password($data);
        if($r){
            // 更新使用者新密碼
            $data['password'] = $data['password_new'];
            $r = $this->users_model->update_password_by_id($data);
            $result = array(
                "status" => 1,
                "msg"=> "修改成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "舊密碼不存在，請重新輸入"
            );    
        }
        return $result;
    }

    // 收藏名片
    public function collect_user_by_userId($data){
        // 預設收藏資訊
        $data['userId'] = $this->session->user_info['id'];
        $data['isCollected'] = 2;
        // 檢查是否已有收藏紀錄
        $r = $this->user_collect_model->check_user_collect($data);
        if(count($r)){
            $result = array(
                "status" => 0,
                "msg"=> "已有收藏紀錄"
            );   
            return $result;
        }
        // 取得欲收藏的使用者資訊
        $collect_user = $this->users_model->get_user_by_id($data['collect_userId']);
        if(count($collect_user)<1){
            $result = array(
                "status" => 0,
                "msg"=> "查無欲收藏名片資訊"
            );
            return $result;
        }
        // 判斷名片是否公開
        $result = array(
            "status" => 1,
            "msg"=> "收藏要求發送成功，待回應",
        );
        if($collect_user[0]->isPublic == '1'){
            $data['isCollected'] = 1;
            $result = array(
                "status" => 1,
                "msg"=> "收藏成功",
            );
        }
        // 新增收藏名片資訊
        $r = $this->user_collect_model->add_user_collect($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "收藏失敗"
            );   
        }
        return $result;
    }

}