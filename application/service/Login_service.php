<?php
class Login_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
    }
    
    public function login($cellphone,$password)
    {
        if ($r = $this->users_model->get_user_by_telpwd($cellphone,$password)){
            $result = array(
                "status" => 1,
                "data"=> $r
            );
        }else{
            $result = array(
                "status" => 0,
                "message"=> "登入失敗"
            );    
        }
        return $result;
    }

    public function logout($cellphone)
    {
        if ($cellphone){
            session_destroy();
        }
    }
}