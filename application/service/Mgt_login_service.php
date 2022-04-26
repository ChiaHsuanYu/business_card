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
                "msg"=> "帳號密碼錯誤"
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
}