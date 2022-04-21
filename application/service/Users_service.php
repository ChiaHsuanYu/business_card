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
        $this->load->service('common_service');
        $this->load->library('session');
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
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "驗證失敗"
            );    
        }
        return $result;
    }

    // 檢查SUPER ID是否重複
    public function check_superId($data){
        $data['id'] = $this->session->user_info['id'];
        $r = $this->users_model->check_superId($data);
        if(!$r){
            $result = array(
                "status" => 1,
                "msg"=> "SUPER ID可使用"
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "SUPER ID已存在，請重新輸入"
            );    
        }
        return $result;
    }
    
    // 修改基本資料
    public function edit_personal_acc($data){
        // 檢查是否需要取得系統預設頭像資料
        if(empty($data['personal_avatar_path'])){
            if($data['personal_avatar_id']){
                $avatar_data = $this->avatar_model->get_avatar_by_id($data['personal_avatar_id']);
                if(count($avatar_data)){
                    $data['personal_avatar_path'] = $avatar_data[0]->imageURL;
                }
            }
        }
        // 新增公司資訊
        $data['id'] = $this->session->user_info['id'];
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
        if($userId){
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

    // 取得使用者資料 by superId
    public function get_user_by_superId($data){
        $r = $this->users_model->get_user_by_superId($data);
        if($r){
            //整理資料-依照順序取得公司資訊 by companyId,userId
            $user_data = $r[0];
            $user_data->companyInfo = array();
            if($user_data->companyOrder){
                for($i=0;$i<count($user_data->companyOrder);$i++){
                    $companyId = $user_data->companyOrder[$i];
                    $company_data = $this->company_model->get_company_by_userId($companyId,$user_data->id);
                    if(count($company_data)){
                        if($company_data[0]->company_social){
                            for($j=0;$j<count($company_data[0]->company_social);$j++){
                                $socialId = $company_data[0]->company_social[$j]->socialId;
                                $social_data = $this->social_model->get_social_by_id($socialId);
                                if(count($social_data)){
                                    $company_data[0]->company_social[$j]->iconURL = $social_data[0]->iconURL;
                                    $company_data[0]->company_social[$j]->socialName = $social_data[0]->name;
                                }
                            }
                        }
                        array_push($user_data->companyInfo,$company_data[0]);
                    }
                }
            }
            if($user_data->personal_social){
                for($i=0;$i<count($user_data->personal_social);$i++){
                    $socialId = $user_data->personal_social[$i]->socialId;
                    $social_data = $this->social_model->get_social_by_id($socialId);
                    if(count($social_data)){
                        $user_data->personal_social[$i]->iconURL = $social_data[0]->iconURL;
                        $user_data->personal_social[$i]->socialName = $social_data[0]->name;
                    }
                }
            }
            $userInfo =  array(
                'userInfo'=>$user_data
            );
            $result = array(
                "status" => 1,
                "data"=> $userInfo
            );  
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );    
        }
        return $result;
    }

    // 編輯個人檔案
    public function update_acc_by_id($data){
        // 檢查是否需要取得系統預設頭像資料
        if(empty($data->personal_avatar_path)){
            if($data->personal_avatar_id){
                $avatar_data = $this->avatar_model->get_avatar_by_id($data->personal_avatar_id);
                if(count($avatar_data)){
                    $data->personal_avatar_path = $avatar_data[0]->imageURL;
                }
            }
        }
        $data->company_order = array();
        $data->id = $this->session->user_info['id'];
        $companyInfo = $data->companyInfo;
        // 刪除公司資訊 by userId
        $this->company_model->del_company_by_userId($data->id);
        for($i=0;$i<count($companyInfo);$i++){
            // 新增公司資訊 for 編輯個人檔案
            $company_data = $data->companyInfo[$i];
            $company_data->order = implode(",",$company_data->order);
            $company_data->company_address = null;
            $company_data->company_phone = null;
            $company_data->company_email = null;
            $company_data->company_social = null;
            if($company_data->company_address){
                $company_data->company_address = implode(",",$company_data->company_address);
            }
            if($company_data->company_phone){
                $company_data->company_phone = implode(",",$company_data->company_phone);
            }
            if($company_data->company_email){
                $company_data->company_email = implode(",",$company_data->company_email);
            }
            if($company_data->company_social){
                $company_data->company_social = json_encode($company_data->company_social);
            }
            $companyId = $this->company_model->add_company_for_acc($data->id, $company_data);
            if($companyId){
                array_push($data->company_order,$companyId);
            }
        }
        // 更新使用者資訊
        $data->order = implode(",",$data->order);
        $data->company_order = null;
        $data->personal_phone = null;
        $data->personal_email = null;
        $data->personal_social = null;
        if($data->company_order){
            $data->company_order = implode(",",$data->company_order);
        }
        if($data->personal_phone){
            $data->personal_phone = implode(",",$data->personal_phone);
        }
        if($data->personal_email){
            $data->personal_email = implode(",",$data->personal_email);
        }
        if($data->personal_social){
        $data->personal_social = json_encode($data->personal_social);
        }
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

    // 更改使用者主題 by userId
    public function update_subjectId_by_id($data){
        $data['id'] = $this->session->user_info['id'];
        $r = $this->users_model->update_subjectId_by_id($data);
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

    // 修改SUPER ID by userId
    public function update_superId_by_id($data){
        $data['id'] = $this->session->user_info['id'];
        $r = $this->users_model->check_superId($data);
        if($r){
            $result = array(
                "status" => 0,
                "msg"=> "SUPER ID已存在，請重新輸入"
            );  
            return $result;
        }
        $r = $this->users_model->update_superId_by_id($data);
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
}