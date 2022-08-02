<?php
class Card_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('user_collect_model');
        $this->load->model('sys_msg_model');
        $this->load->model('user_msg_state_model');
        $this->load->model('county_model');
        $this->load->model('scan_record_model');
        $this->load->service('Common_service');
        $this->load->library('session');
        $this->load->driver('cache');
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
        $notify_user = $this->users_model->get_user_by_id($data['userId']);
        $notify_data = array(
            'title' => "收藏通知",
            'date' => date('Y-m-d H:i:s'),
        );
        if($collect_user[0]->isPublic == '1'){
            $data['isCollected'] = 1;
            $result = array(
                "status" => 1,
                "msg"=> "收藏成功",
            );
            $notify_data['msg'] =  $notify_user[0]->personal_superID."已收藏您的名片";
        }else{
            $notify_data['msg'] =  $notify_user[0]->personal_superID."已要求收藏您的名片";
        }
        $result['notify_data'] = $notify_data;
        $result['data'] = $data;
        $this->common_service->add_notify_cache($data['userId'],$notify_data);
        $result['cache'] = $this->common_service->check_notify();
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

    // 更新名片收藏狀態
    public function update_isCollected_by_id($data){
        $r = $this->user_collect_model->update_isCollected_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "更新失敗"
            );    
            return $result;
        }
        // 取得名片收藏資訊 by id
        $status = '拒絕';
        $collect_data = $this->user_collect_model->get_user_collect_by_id($data);
        if($collect_data->isCollected == '1'){
            $status = '接受';
        }
        $msg_data = array(
            'userId' => $collect_data->userId,
            'title' => '收藏要求回覆',
            'msg' => $collect_data->superID.'已'.$status.'您的收藏要求',
        );
        // 新增系統通知訊息
        $insertId = $this->sys_msg_model->add_sys_msg($msg_data);
        if(!$insertId){
            $result = array(
                "status" => 0,
                "msg"=> "更新成功，新增系統通知訊息失敗"
            );    
            return $result;
        }
        // 記錄緩存並設置存活時間
        $this->cache->redis->save($insertId.'_'.$collect_data->userId,0,TIME_TO_LIVE);
        // 緩存通知訊息
        $notify_data = array(
            'title' => $msg_data['title'],
            'msg' => $msg_data['msg'],
            'date' => date('Y-m-d H:i:s'),
        );
        $this->common_service->add_notify_cache($collect_data->userId,$notify_data);
        $result = array(
            "status" => 1,
            "msg"=> "更新成功"
        );  
        return $result;
    }

    // 取得收藏要求清單
    public function get_collect_by_userId(){
        $data['userId'] = $this->session->user_info['id'];
        $data['isCollected'] = 2;
        $collect_user_data = $this->user_collect_model->get_collect_by_collect_userId($data);
        if(!count($collect_user_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );  
            return $result;
        }
        // 依序取得公司職位名稱
        foreach($collect_user_data as $key => $value){
            $value = $this->get_company_data($value);
            $collect_user_data[$key] = $value;
        }
        $result = array(
            "status" => 1,
            "data"=> $collect_user_data
        );   
        return $result;
    }

    // 取得公司職位
    public function get_company_data($user_data){
        $user_data->position = '';
        $companyOrder = $user_data->companyOrder;
        if(!$companyOrder || !count($companyOrder)){
            return $user_data;
        }
        $companyId = $companyOrder[0];
        $company_data = $this->company_model->get_company_by_userId($companyId,$user_data->userId);
        if(count($company_data)){
            $user_data->position = $company_data[0]->company_position;
        }
        return $user_data;
    }

    // 查詢使用者ID
    public function query_users_id($data){
        $data['userId'] = $this->session->user_info['id'];
        // 查詢指定userId外的使用者ID
        $all_users = $this->users_model->get_random_users($data);
        if(!count($all_users)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 檢查公司是否符合篩選條件
        $users_id = $this->check_company($all_users,$data);
        //記錄緩存並設置存活時間
        $this->cache->redis->save('id_'.$data['userId'],$users_id,TIME_TO_LIVE);
        if(!count($users_id)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
        }else{
            $result = array(
                "status" => 1,
                "msg"=> "成功緩存隨機使用者ID"
            );   
        }
        return $result;
    }

    // 取得隨機名片列表
    public function query_user($data){
        // 依據欲查詢位置取得使用者id
        $data['userId'] = $this->session->user_info['id'];
        $users_id = $this->cache->redis->get('id_'.$data['userId']);
        if(!count($users_id)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 取得使用者資訊
        $all_user_data = $this->get_output_user($users_id,$data,1);
        if(!count($all_user_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
        }else{
            $result = array(
                "status" => 1,
                "data"=> $all_user_data
            );   
        }
        return $result;
    }

    // 取得收藏名片列表
    public function query_user_collect($data){
        // 查詢指定userId外的使用者ID
        $data['userId'] = $this->session->user_info['id'];
        $all_users = $this->user_collect_model->get_collect_users($data['userId']);
        if(!count($all_users)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 檢查公司是否符合篩選條件
        $collect_users_id = $this->check_company($all_users,$data);
        if(!count($collect_users_id)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 取得使用者資訊
        $all_user_data = $this->get_output_user($collect_users_id,$data);
        if(!count($all_user_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
        }else{
            $result = array(
                "status" => 1,
                "data"=> $all_user_data
            );   
        }
        return $result;
    }

    // 取得被收藏的使用者清單
    public function get_user_for_collected(){
        // 查詢指定userId外的使用者ID
        $data['userId'] = $this->session->user_info['id'];
        $all_users = $this->user_collect_model->get_user_for_collected($data['userId']);
        if(!count($all_users)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 依序取得使用者資訊
        $all_user_data = array();
        foreach($all_users as $key => $value){
            // 依序取得公司職位名稱
            $personal_data = new Card_service();
            $personal_data->collectId = $value->collectId;
            $personal_data->userId = $value->id;
            $personal_data->name = $value->personal_name;
            $personal_data->superID = $value->personal_superID;
            $personal_data->nickname = $value->personal_nickname;
            $personal_data->avatar = $value->personal_avatar;
            $personal_data->companyOrder = $value->companyOrder;
            $personal_data = $this->get_company_data($personal_data);
            array_push($all_user_data,$personal_data);
        }
        if(!count($all_user_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
        }else{
            $result = array(
                "status" => 1,
                "data"=> $all_user_data
            );   
        }
        return $result;
    }

    // 檢查公司是否符合篩選條件
    public function check_company($all_users,$data){
        // 取得指定縣市名稱
        $data['area_name'] = '';
        if($data['areaId']){
            $area = $this->county_model->get_county_by_id($data['areaId']);
            if(count($area)){
                $data['area_name'] = $area[0]->name;
            }
        }
        $users_id = array();
        foreach($all_users as $key => $value){
            // 判斷公司資訊是否符合查詢條件
            $companyOrder = $value->companyOrder;
            if((!$companyOrder || !count($companyOrder)) || (empty($data['areaId']) && empty($data['industryId']))){
                array_push($users_id,$value->id);
                continue;
            }
            foreach($companyOrder as $company_key => $companyId){
                $company = $this->company_model->check_company_for_random($companyId,$data);
                if($company){
                    array_push($users_id,$value->id);
                    continue;
                }
            }
        }
        return $users_id;
    }

    // 取得使用者資訊
    public function get_output_user($users_id,$data,$random = 0){
        if($random){
            $users_id = array_slice($users_id,$data['start_index'],$data['length']);
        }
        $all_user_data = array();
        // 依序取得使用者資訊
        foreach($users_id as $key => $value){
            $user_data = $this->users_model->get_user_by_id($value);
            // 依序取得公司職位名稱
            $personal_data = array();
            if(count($user_data)){
                $personal_data = new Card_service();
                $user_value = $user_data[0];
                $personal_data->userId = $user_value->id;
                $personal_data->name = $user_value->personal_name;
                $personal_data->superID = $user_value->personal_superID;
                $personal_data->nickname = $user_value->personal_nickname;
                $personal_data->avatar = $user_value->personal_avatar;
                $personal_data->companyOrder = $user_value->companyOrder;
                $personal_data = $this->get_company_data($personal_data);
            }
            if($random){
                // 取得收藏狀態
                $personal_data->isCollected = 0;
                $check_data = array(
                    "userId" => $data['userId'],
                    "collect_userId" => $value
                );
                $collect_result = $this->user_collect_model->check_user_collect($check_data);
                if(count($collect_result)){
                    $personal_data->isCollected =  $collect_result[0]->isCollected;
                }
            }
            array_push($all_user_data,$personal_data);
        }
        return $all_user_data;
    }

    public function add_scan_record($data){
        $data['userId'] = $this->session->user_info['id'];
        $check_result = $this->scan_record_model->check_scan_record($data);
        if(count($check_result)){
            $data['id'] = $check_result[0]->id;
            $r = $this->scan_record_model->update_scan_record_by_id($data);
        }else{
            $r = $this->scan_record_model->add_scan_record($data);
        }
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "瀏覽紀錄更新失敗"
            );   
            return $result;
        }
        $result = array(
            "status" => 0,
            "msg"=> "瀏覽紀錄更新成功"
        ); 
        return $result;
    }

    // 取得瀏覽紀錄列表
    public function query_scan_record($data){
        $data['userId'] = $this->session->user_info['id'];
        $all_users = $this->scan_record_model->get_scan_record($data['userId']);
        if(!count($all_users)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 檢查公司是否符合篩選條件
        $collect_users_id = $this->check_company($all_users,$data);
        if(!count($collect_users_id)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
            return $result;
        }
        // 取得使用者資訊
        $all_user_data = $this->get_output_user($collect_users_id,$data);
        if(!count($all_user_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );   
        }else{
            $result = array(
                "status" => 1,
                "data"=> $all_user_data
            );   
        }
        return $result;
    }
}