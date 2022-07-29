<?php
class Sys_msg_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Common_service");
        $this->load->model('users_model');
        $this->load->model('sys_msg_model');
        $this->load->model('user_msg_state_model');
        $this->load->driver('cache');
    }

    // 新增系統通知訊息
    public function add_sys_msg($data){
        $insertId = $this->sys_msg_model->add_sys_msg($data);
        if(!$insertId){
            $result = array(
                "status" => 0,
                "msg"=> "新增失敗"
            );    
            return $result;
        }
        // 紀錄系統通知訊息已讀狀態&對象
        $all_user = $this->users_model->get_users();
        foreach($all_user as $key => $value){
            $this->cache->redis->save($insertId.'_'.$value->id,0,TIME_TO_LIVE); //記錄緩存並設置存活時間
            // 緩存通知訊息
            $notify_data = array(
                'title' => $data['title'],
                'msg' => $data['msg'],
                'date' => date('Y-m-d H:i:s'),
            );
            $this->common_service->add_notify_cache($value->id,$notify_data);
        }
        $result = array(
            "status" => 1,
            "msg"=> "新增成功"
        );  
        return $result;
    }

    // 修改系統通知訊息
    public function update_sys_msg_by_id($data){
        $r = $this->sys_msg_model->update_sys_msg_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );    
            return $result;
        }
        // 紀錄系統通知訊息已讀狀態&對象
        $all_user = $this->users_model->get_users();
        foreach($all_user as $key => $value){
            $this->cache->redis->save($data['id'].'_'.$value->id,0,TIME_TO_LIVE); //記錄緩存並設置存活時間
            // 緩存通知訊息
            $notify_data = array(
                'title' => "【修改系統通知】".$data['title'],
                'msg' => $data['msg'],
                'date' => date('Y-m-d H:i:s'),
            );
            $this->common_service->add_notify_cache($value->id,$notify_data);
        }
        $result = array(
            "status" => 1,
            "msg"=> "修改成功"
        );  
        return $result;
    }
    // 刪除系統通知訊息
    public function delete_sys_msg_by_id($data){
        $r = $this->sys_msg_model->delete_sys_msg_by_id($data['id']);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "刪除失敗"
            );    
            return $result;
        }
        // 取得所有對象的已讀狀態
        $all_user = $this->users_model->get_users();
        foreach($all_user as $key => $value){
            $this->cache->delete($data['id'].'_'.$value->id); //刪除緩存
        }
        $result = array(
            "status" => 1,
            "msg"=> "刪除成功"
        );  
        return $result;
    }
    // 系統通知訊息列表
    public function query_sys_msg($data){
        $r = $this->sys_msg_model->query_sys_msg($data);
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
}