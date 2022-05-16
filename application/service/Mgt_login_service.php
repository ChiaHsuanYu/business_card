<?php
class Mgt_login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Common_service");
        $this->load->model('mgt_users_model');
        $this->load->model('mgt_login_model');
    }

    // 登入
    public function login($account,$password){
        if ($r = $this->mgt_users_model->get_user_by_accpwd($account,$password)){
            // 寫入登入紀錄
            $host = $this->common_service->get_ip();
            $this->common_service->logger("account:".$account.",IP:".$host);
            // $this->check_mgt_login($r[0]->id);
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

    // 登出
    public function logout($account){
        if ($account){
            // 寫入登入紀錄
            $host = $this->common_service->get_ip();
            $this->common_service->logger("account:".$account.",IP:".$host);
            $this->session->unset_userdata('mgt_user_info');
            // $this->session->sess_destroy();
        }
    }

    // 新增登入紀錄 (登入紀錄寫在資料庫的做法)
    public function check_mgt_login($id){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $host = $_SERVER['HTTP_CLIENT_IP'];
        }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $host = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $host= $_SERVER['REMOTE_ADDR'];
        }
        // if($r = $this->mgt_login_model->check_login_by_userId($id,$host)){
        //     $this->mgt_login_model->update_login_by_id($r[0]->id);
        // }else{
            $this->mgt_login_model->add_mgt_login($id,$host);
        // }
        $result = array(
            "status" => 1,
            "msg"=> "新增登入紀錄"
        );
        return $result;
    }

}