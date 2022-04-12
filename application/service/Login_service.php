<?php
class Login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
    }
    
    public function login($account,$password)
    {
        if ($r = $this->users_model->get_user_by_accpwd($account,$password)){
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

    public function logout($account)
    {
        if ($account){
            session_destroy();
        }
    }
}