<?php
class Mgt_login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("register_service");
        $this->load->model('mgt_users_model');
        $this->load->model('social_model');
    }

    public function login($account,$password){
        if ($r = $this->mgt_users_model->get_user_by_accpwd($account,$password)){
            $result = array(
                "status" => 1,
                "data"=> $r
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "登入失敗"
            );    
        }
        return $result;
    }

    // 帳號驗證
    public function account_verify($data){
        if($this->session->account_data){
            $data['userId'] = $this->session->account_data['id'];
            $r = $this->mgt_users_model->check_verify_by_id($data);
            if($r){
                $this->mgt_users_model->update_verify_by_id($data['userId']);
                $result = array(
                    "status" => 1,
                    "data"=> $r
                );
            }else{
                $result = array(
                    "status" => 0,
                    "msg"=> "手機驗證失敗"
                );
            }
        }else{
            $result = array(
                "status" => 3,
                "msg"=> "尚未註冊手機號碼"
            );
        }
        return $result;
    }

    // 登出
    public function logout($account){
        if ($account){
            $this->session->unset_userdata('mgt_user_info');
            // $this->session->sess_destroy();
        }
    }

    // 整理使用者資料 by companyId,userId
    public function get_company($user_data){
        $user_data->companyInfo = array();
        // 依照順序取得公司資訊
        if($user_data->companyOrder){
            for($i=0;$i<count($user_data->companyOrder);$i++){
                $companyId = $user_data->companyOrder[$i];
                $company_data = $this->company_model->get_company_by_userId($companyId,$user_data->id);
                if(count($company_data)){
                    // 依序取得公司社群icon
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
        // 依序取得個人社群icon
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
        return $userInfo;
    }
}