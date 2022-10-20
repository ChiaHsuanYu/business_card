<?php
class Users_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('company_model');
        $this->load->model('social_model');
        $this->load->model('avatar_model');
        $this->load->model('sys_msg_model');
        $this->load->model('user_collect_model');
        $this->load->service('Gps_service');
        $this->load->service('Common_service');
        $this->load->library('session');
        $this->load->driver('cache', array('adapter' => 'redis','backup' => 'file'));
    }
   
    // 帳號驗證
    public function account_verify($data){
        $r = $this->users_model->check_verify_by_account($data);
        if($r){
            $r = $this->users_model->update_verify_by_id($r[0]->id);
            $result = array(
                "status" => 1,
                "msg"=> "驗證成功"
            );  
            return $result;
        }
        $result = array(
            "status" => 0,
            "msg"=> "驗證失敗"
        );    
        return $result;
    }

    // 檢查SUPER ID是否重複
    public function check_superId($data){
        $data['id'] = $this->common_service->get_userId_for_session();
        $r = $this->users_model->check_superId($data);
        if(!$r){
            $result = array(
                "status" => 1,
                "msg"=> "SUPER ID可使用"
            );  
            return $result;
        }
        $result = array(
            "status" => 0,
            "msg"=> "SUPER ID已存在，請重新輸入"
        );   
        return $result; 
    }
    
    // 修改基本資料
    public function edit_personal_acc($data){
        // 檢查是否需要取得系統預設頭像資料
        $data['personal_avatar_path'] = $this->check_avatar_data($data['personal_avatar_path'],$data['personal_avatar_id']);
        // 檢查是否已有上傳過圖片
        $data['personal_avatar_path'] = $this->check_orig_img($data['personal_avatar_path'],$data['personal_orig_img']);
        // 新增公司資訊
        $data['id'] = $this->common_service->get_userId_for_session();
        $companyId = $this->company_model->add_company($data);
        if(!$companyId){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );    
            return $result;
        }
        // 檢查SUPER ID是否重複
        $data['superId'] = $data['personal_superID'];
        $r = $this->users_model->check_superId($data);
        if($r){
            $result = array(
                "status" => 0,
                "msg"=> "SUPER ID已存在，請重新輸入"
            );  
            return $result;
        }
        // 更新個人資訊
        $data['companyOrder'] = $companyId;
        $userId = $this->users_model->update_personal_by_id($data);
        if(!$userId){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗",
            );
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> "修改成功",
        );  
        return $result;
    }

    // 檢查是否需要取得系統預設頭像資料
    public function check_avatar_data($personal_avatar_path,$personal_avatar_id){
        if(empty($personal_avatar_path) && !empty($personal_avatar_id)){
            $avatar_data = $this->avatar_model->get_avatar_by_id($personal_avatar_id);
            if(count($avatar_data)){
                $personal_avatar_path = $avatar_data[0]->imageURL;
            }
        }
        return $personal_avatar_path;
    }

    // 檢查是否已有上傳過圖片
    public function check_orig_img($personal_avatar_path,$personal_orig_img){
        if(isset($personal_orig_img) && !empty($personal_orig_img)){
            $avatar_path = explode(base_url().AVATAR_PATH,$personal_orig_img);
            // $avatar_path = explode(AVATAR_PATH,$personal_orig_img);
            if(count($avatar_path)>1){
                $personal_avatar_path = $avatar_path[1];
            }else{
                $personal_avatar_path = $avatar_path[0];
            }
        }
        return $personal_avatar_path;
    }

    // 修改密碼
    public function update_password($data){
        // 檢查使用者舊密碼
        $data['id'] = $this->common_service->get_userId_for_session();
        $r = $this->users_model->check_user_by_password($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "舊密碼不存在，請重新輸入"
            );    
            return $result;
        }
        // 更新使用者新密碼
        $data['password'] = $data['password_new'];
        $r = $this->users_model->update_password_by_id($data);
        $result = array(
            "status" => 1,
            "msg"=> "修改成功"
        );  
        return $result;
    }

    // 取得使用者資料 by superId
    public function get_user_by_superId($data){
        $r = $this->users_model->get_user_by_superId($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );    
            return $result;
        }
        $user_data = $r[0];
        // 檢查用戶關係
        $result = $this->check_user_relation($data,$user_data);
        if($result['status'] != 1){
            return $result;
        }
        // 整理資料-依照順序取得公司資訊 by companyId,userId
        $user_data = $this->sort_companyInfo($user_data);
        // 整理資料-取得社群資訊
        $user_data->personal_social = $this->get_social_data($user_data->personal_social);
        $userInfo =  array(
            'userInfo'=>$user_data
        );
        $result = array(
            "status" => 1,
            "data"=> $userInfo
        );  
        return $result;
    }

    // 檢查用戶關係
    public function check_user_relation($data,$user_data){
        if(!$user_data->isPublic){
            // 檢查是否有登入紀錄
            $result = array(
                "status" => 2,
                "msg"=> "用戶帳號不公開",
            );   
            if(!isset($this->session->user_info['id'])){
                return $result;
            }
            $data['userId'] = $this->common_service->get_userId_for_session();
            $data['collect_userId'] = $user_data->id;
            // 檢查是否已有收藏紀錄
            $collect_data = $this->user_collect_model->check_user_collect($data);
            if(count($collect_data) < 1){
                return $result;
            }
            // 檢查是否尚未接受要求
            if($collect_data[0]->isCollected == 2){
                $result = array(
                    "status" => 3,
                    "data"=> "已送出收藏請求"
                );    
                return $result;
            }
        }
        $result['status'] = 1; 
        return $result;
    }

    // 取得使用者資料-依照順序取得公司資訊 by companyId,userId
    public function sort_companyInfo($user_data){
        $user_data->companyInfo = array();
        if($user_data->companyOrder){
            for($i=0;$i<count($user_data->companyOrder);$i++){
                $companyId = $user_data->companyOrder[$i];
                $company_data = $this->company_model->get_company_by_userId($companyId,$user_data->id);
                if(count($company_data)){
                    // 整理資料-取得社群資訊 by socialId
                    $company_data[0]->company_social = $this->get_social_data($company_data[0]->company_social);
                    array_push($user_data->companyInfo,$company_data[0]);
                }
            }
        }
        return $user_data;
    }

    // 取得使用者資料-取得社群資訊 by socialId
    public function get_social_data($social_data){
        if($social_data){
            for($i=0;$i<count($social_data);$i++){
                $socialId = $social_data[$i]->socialId;
                $social_data = $this->social_model->get_social_by_id($socialId);
                if(count($social_data)){
                    $social_data[$i]->iconURL = $social_data[0]->iconURL;
                    $social_data[$i]->socialName = $social_data[0]->name;
                }
            }
        }
        return $social_data;
    }

    // 編輯個人檔案
    public function update_acc_by_id($data){
        $data->company_order = array();
        $data->id = $this->common_service->get_userId_for_session();
        // 檢查是否需要取得系統預設頭像資料
        $data->personal_avatar_path = $this->check_avatar_data($data->personal_avatar_path,$data->personal_avatar_id);
        if($data->personal_avatar_path){
            $this->avatar_rename($data->id);  
        }
        // 更新公司資訊
        $data = $this->update_companyInfo($data);
        // 更新使用者資訊
        $data->order = $this->common_service->str_implode(",",$data->order);
        $data->company_order = $this->common_service->str_implode(",",$data->company_order);
        $data->personal_phone = $this->common_service->str_implode(",",$data->personal_phone);
        $data->personal_email = $this->common_service->str_implode(",",$data->personal_email);
        $data->personal_social = $this->common_service->str_json_encode($data->personal_social);
        // 檢查是否已有上傳過圖片
        $data->personal_avatar_path = $this->check_orig_img($data->personal_avatar_path,$data->personal_orig_img);
        // 移動頭像檔案
        if(empty($data->personal_avatar_path)){
            $this->avatar_rename($data->id);  
        }
        // 更新個人檔案
        $r = $this->users_model->update_acc_by_id($data);
        if($r){
            $result = array(
                "status" => 1,
                "msg"=> "更新成功"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "更新失敗"
            );    
        }
        return $result;
    }

    // 編輯個人檔案-更新公司資訊
    public function update_companyInfo($data){
        $companyInfo = $data->companyInfo;
        for($i=0;$i<count($companyInfo);$i++){
            // 新增公司資訊 for 編輯個人檔案
            $company_data = $companyInfo[$i];
            if(!empty($company_data->del_id)){
                // 移動LOGO圖片
                if($company_data->del_id){
                    $this->logo_rename($company_data->del_id,$data->id);
                }
                $this->company_model->del_company_by_id($company_data->del_id); // 刪除公司資訊 by id
            }else{
                // 陣列轉換字串
                $company_data->order = $this->common_service->str_implode(",",$company_data->order);
                $company_data->company_address = $this->common_service->str_implode(",",$company_data->company_address);
                $company_data->company_phone = $this->common_service->str_implode(",",$company_data->company_phone);
                $company_data->company_email = $this->common_service->str_implode(",",$company_data->company_email);
                $company_data->company_social = $this->common_service->str_json_encode($company_data->company_social);
                // 檢查是否已有上傳過圖片
                if(isset($company_data->company_orig_logo) && !empty($company_data->company_orig_logo)){
                    $company_logo_path = explode( base_url().LOGO_PATH,$company_data->company_orig_logo);
                    $company_data->company_logo_path = $company_logo_path[1];
                }
                // 移動LOGO圖片
                if(empty($company_data->company_logo_path) && !empty($company_data->id)){
                    if($company_data->id){
                        $this->logo_rename($company_data->id,$data->id);
                    }
                }
                // 新增or更新公司資訊
                if($company_data->id){
                    $companyId = $company_data->id;
                    $this->company_model->update_company_for_id($company_data);
                }else{
                    $companyId = $this->company_model->add_company_for_acc($data->id, $company_data);
                }
                // 記錄公司順序
                if($companyId){
                    array_push($data->company_order,$companyId);
                }
            }
        }
        return $data;
    }
    
    // 移動頭像檔案
    public function avatar_rename($id){
        $r = $this->users_model->get_user_by_id($id);
        if(!count($r)){
            return false;
        }
        if($r[0]->personal_avatar){
            $old_file_path = explode(base_url(), $r[0]->personal_avatar);
            $old_file_name = explode(AVATAR_PATH, $old_file_path[1]);
            // $old_file_name = explode(AVATAR_PATH, $r[0]->personal_avatar);
            if(count($old_file_name)>1 && file_exists(AVATAR_PATH.$old_file_name[1]) && !file_exists(SYSTEM_AVATAR_PATH.$old_file_name[1])){
                $path = DEL_AVATAR_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                rename(AVATAR_PATH.$old_file_name[1],DEL_AVATAR_PATH.$old_file_name[1]);
            }
        }
        return true;
    }

    // 移動LOGO檔案
    public function logo_rename($companyId,$userId){
        $r = $this->company_model->get_company_by_userId($companyId,$userId);
        if(!count($r)){
            return false;
        }
        $old_file_path = explode(base_url(), $r[0]->company_logo);
        if(count($old_file_path)>1){
            $old_file_name = explode(LOGO_PATH, $old_file_path[1]);
            // $old_file_name = explode(LOGO_PATH, $r[0]->company_logo);
            if(count($old_file_name)>1 && file_exists(LOGO_PATH.$old_file_name[1])){
                $path = DEL_LOGO_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                rename(LOGO_PATH.$old_file_name[1],DEL_LOGO_PATH.$old_file_name[1]);
            }
        }
        return true;
    }

    // 更改使用者主題 by userId
    public function update_subjectId_by_id($data){
        $data['id'] = $this->common_service->get_userId_for_session();
        $r = $this->users_model->update_subjectId_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );    
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> "修改成功"
        );  
        return $result;
    }

    // 修改SUPER ID by userId
    public function update_superId_by_id($data){
        $data['id'] = $this->common_service->get_userId_for_session();
        $r = $this->users_model->check_superId($data);
        if($r){
            $result = array(
                "status" => 0,
                "msg"=> "SUPER ID已存在，請重新輸入"
            );  
            return $result;
        }
        $r = $this->users_model->update_superId_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );    
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> "修改成功"
        );  
        return $result;
    }

    // 更改隱私設定 by userId
    public function update_isPublic_by_userId($data){
        $data['id'] = $this->common_service->get_userId_for_session();
        $r = $this->users_model->update_isPublic_by_id($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );   
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> "修改成功",
        );  
        return $result;
    }

    // 取得系統通知訊息 by userId
    public function get_sys_msg_by_userId(){
        $userId = $this->common_service->get_userId_for_session();
        $sys_msgs = $this->sys_msg_model->get_sys_msg_by_userId($userId);
        if(!$sys_msgs){
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );    
            return $result;
        }
        // 取得使用者已讀狀態
        foreach($sys_msgs as $key => $value){
            $value->isReaded = '1';
            $cache_status = $this->cache->redis->get($value->id.'_'.$userId);
            if($cache_status === '0'){
                $value->isReaded = '0';
            }
            $sys_msgs[$key] = $value;
        }
        $result = array(
            "status" => 1,
            "data"=> $sys_msgs
        );  
        return $result;
    }

    // 取得未讀通知總數 by userId
    public function get_msg_count_by_userId(){
        // 取得當前使用者ID及所有系統訊息
        $userId = $this->common_service->get_userId_for_session();
        $sys_msgs = $this->sys_msg_model->get_sys_msg_by_userId($userId);
        if(!$sys_msgs){
            $result = array(
                "status" => 0,
                "msg_count" => 0
            );   
            return $result;
        }
        // 判斷cache key是否存在+已讀狀態是否為0
        $msg_count = 0;
        foreach($sys_msgs as $key => $value){
            $cache_status = $this->cache->redis->get($value->id.'_'.$userId);
            if($cache_status === '0'){
                $msg_count++;
            }
        }
        $result = array(
            "status" => 1,
            "msg_count"=> $msg_count
        );  
        return $result;
    }
    // 修改系統訊息已讀狀態 by userId
    public function update_sys_msg_isReaded_by_id($data){
        $userId = $this->common_service->get_userId_for_session();
        $this->cache->delete($data['msgId'].'_'.$userId);
        $result = array(
            "status" => 1,
            "msg"=> '修改成功'
        );  
        return $result;
    }

    // 修改收藏要求已讀狀態
    public function update_collect_isReaded_by_id($data){
        $r = $this->user_collect_model->update_collect_isReaded_by_id($data['collectId']);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> '修改失敗'
            );  
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> '修改成功'
        );  
        return $result;
    }

    // 更改AI推薦設定
    public function update_isOpenAI($data){
        $data['userId'] = $this->common_service->get_userId_for_session();
        $r = $this->users_model->update_isOpenAI($data);
        if(!$r){
            $result = array(
                "status" => 0,
                "msg"=> '修改失敗'
            );  
            return $result;
        }
        $result = array(
            "status" => 1,
            "msg"=> '修改成功'
        );  
        // 開啟AI推薦功能時需先將該使用者相關的房間資訊檢查時間改成當下時間
        if($data['isOpenAI'] == '1'){
            // 取得親密度累積設定
            $gps_room = $this->cache->redis->get('gps_room');
            $contact_setting = $this->contact_setting_model->get_contact_setting();
            $max_time = MAX_CONTACT_TIME;
            if($contact_setting){
                $max_time = $contact_setting[0]->max_contact_time;
            }
            // 取得需檢查的人員清單(有開啟AI推薦的使用者/尚未收藏使用者/未在取消接觸累積的使用者列表內/當日接觸時間尚小於max_time的使用者)
            $other_users = $this->users_model->get_other_users_for_gps($data['userId'],$max_time);
            foreach($other_users as $value){
                $other_id = $value->id;
                // 檢查是否已有接觸紀錄
                $room_key = '';
                $nowtime = strtotime(date('Y-m-d H:i:s'));
                if(array_key_exists('room_'.$other_id.'_'.$data['userId'],$gps_room)){
                    $room_key = 'room_'.$other_id.'_'.$data['userId'];
                }
                if(array_key_exists('room_'.$data['userId'].'_'.$other_id,$gps_room)){
                    $room_key = 'room_'.$data['userId'].'_'.$other_id;
                }
                if($room_key){
                    $gps_room[$room_key]['last_check_time'] = $nowtime;
                }
            }
            $this->cache->redis->save('gps_room',$gps_room,TIME_TO_LIVE);
            $result['gps_room'] = $gps_room;
        }
        return $result;
    }

    // 更新裝置GPS定位
    public function update_gps($data){
        // $userId = $data['userId'];
        $userId = $this->common_service->get_userId_for_session();
        if(isset($this->input->request_headers()['x-forwarded-for'])){
            $host = $this->input->request_headers()['x-forwarded-for'];
        }else if(isset($this->input->request_headers()['Host'])){
            $host = $this->input->request_headers()['Host'];
        }else{
            $host = '';
        }
        $data['userId'] = $userId;
        $data['host'] = $host;
        $gps_data = $this->cache->redis->get('gps_data'); //取得gps定位緩存
        $gps_data[$userId .'_gps'] = $data;
        $this->cache->redis->save('gps_data',$gps_data,TIME_TO_LIVE); //記錄gps定位緩存
        
        // 與其他使用者位置比對
        $result = $this->gps_service->check_gps($userId,$gps_data);
        $result['user'] = $data;
        // $cache_info = $this->cache->redis->cache_info();
        // $result['cache_info'] = $cache_info['used_memory_human'];
        return $result;
    }
}