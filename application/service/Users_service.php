<?php
class Users_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('company_model');
        $this->load->model('social_model');
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
        $data['id'] = $this->session->user_info['id'];
        $companyId = $this->company_model->add_company($data);
        if(!$companyId){
            $result = array(
                "status" => 0,
                "msg"=> "修改失敗"
            );    
            return $result;
        }
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
}