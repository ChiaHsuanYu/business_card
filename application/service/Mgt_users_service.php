<?php
class Mgt_users_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mgt_users_model');
        $this->load->model('users_model');
        $this->load->model('social_model');
        $this->load->model('industry_model');
        $this->load->service('common_service');
        $this->load->library('session');
    }

    // 取得產業類別
    public function get_industry(){
        $this->common_service->logger("get_industry");
        $r = $this->industry_model->query_all();
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

    // 修改密碼
    public function update_password($data){
        // 檢查使用者舊密碼
        $data['id'] = $this->session->mgt_user_info['id'];
        $r = $this->mgt_users_model->check_user_by_password($data);
        if($r){
            // 更新使用者新密碼
            $this->common_service->logger("user_id:".$data['id']);
            $data['password'] = $data['password_new'];
            $r = $this->mgt_users_model->update_password_by_id($data);
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

    // 修改使用者帳號狀態(凍結/解凍) by userId
    public function update_isDeleted_by_id($data){
        $this->common_service->logger("userId:".$data['userId'].",isDeleted:".$data['isDeleted']);
        $r = $this->users_model->update_isDeleted_by_id($data);
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

    // 使用者名片查詢
    public function query_users($data){
        $this->common_service->logger("query_users");
        $r = $this->users_model->query_users($data);
        if($r['total_count']){
            for($m=0;$m<count($r['users']);$m++){
                //整理資料-依照順序取得公司資訊 by companyId,userId
                $user_data = $r['users'][$m];
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
                $r['users'][$m] = $user_data;
            }
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
}