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
        // 紀錄系統通知訊息的通知名單
        $all_user = $this->users_model->get_users();
        $msg_isReaded_list = $this->cache->redis->get('msg_isReaded_list'); //取得系統通知已讀狀態的緩存資料
        $all_notify_list = $this->cache->redis->get('notify_list'); //取得其他系統通知的緩存資料
        if(!$msg_isReaded_list){
            $msg_isReaded_list = array();
        }
        if(!$all_notify_list){
            $all_notify_list = array();
        }
        // 整理系統通知訊息
        $notify_data = array(
            'title' => $data['title'],
            'msg' => $data['msg'],
            'date' => date('Y-m-d H:i:s'),
        );
        foreach($all_user as $value){
            // 預設為未讀狀態0
            $msg_isReaded_list[$insertId.'_'.$value->id] = 0;
            // 判斷是否已有其它通知訊息
            $notify_list = array();
            if(array_key_exists('notify_'.$value->id,$all_notify_list)){
                $notify_list = $all_notify_list['notify_'.$value->id];
            }
            array_push($notify_list,$notify_data);
            $all_notify_list['notify_'.$value->id] = $notify_list;
        }
        $this->cache->redis->save('msg_isReaded_list',$msg_isReaded_list,NOTIFY_TIME_TO_LIVE); //記錄通知訊息已讀狀態
        $this->cache->redis->save('notify_list',$all_notify_list,NOTIFY_TIME_TO_LIVE); //記錄緩存通知訊息
        $result = array(
            "status" => 1,
            "msg"=> "新增成功",
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
        // 紀錄系統通知訊息的通知名單
        $all_user = $this->users_model->get_users();
        $msg_isReaded_list = $this->cache->redis->get('msg_isReaded_list'); //取得系統通知已讀狀態的緩存資料
        $all_notify_list = $this->cache->redis->get('notify_list'); //取得其他系統通知的緩存資料
        if(!$msg_isReaded_list){
            $msg_isReaded_list = array();
        }
        if(!$all_notify_list){
            $all_notify_list = array();
        }
        $notify_data = array(
            'title' => "【修改系統通知】".$data['title'],
            'msg' => $data['msg'],
            'date' => date('Y-m-d H:i:s'),
        );
        foreach($all_user as $value){
            // 預設為未讀狀態0
            $msg_isReaded_list[$data['id'].'_'.$value->id] = 0;
           // 判斷是否已有其它通知訊息
           $notify_list = array();
           if(array_key_exists('notify_'.$value->id,$all_notify_list)){
               $notify_list = $all_notify_list['notify_'.$value->id];
           }
           array_push($notify_list,$notify_data);
           $all_notify_list['notify_'.$value->id] = $notify_list;
        }
        $this->cache->redis->save('msg_isReaded_list',$msg_isReaded_list,NOTIFY_TIME_TO_LIVE); //記錄緩存並設置存活時間
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
        $msg_isReaded_list = $this->cache->redis->get('msg_isReaded_list'); //取得緩存
        if($msg_isReaded_list){
            foreach($all_user as $value){
                unset($msg_isReaded_list[$data['id'].'_'.$value->id]);//刪除緩存
            }
        }
        $this->cache->redis->save('msg_isReaded_list',$msg_isReaded_list,NOTIFY_TIME_TO_LIVE); //記錄緩存並設置存活時間
        $result = array(
            "status" => 1,
            "msg"=> "刪除成功"
        );  
        return $result;
    }

    // 取得系統通知訊息
    public function get_sys_msg($data){
        $r = $this->sys_msg_model->get_sys_msg($data['id']);
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

    // 系統通知訊息列表
    public function query_sys_msg($data){
        $r = $this->sys_msg_model->query_sys_msg($data);
        if(!$r['total_count']){
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