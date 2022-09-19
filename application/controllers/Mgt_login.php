<?php
require_once APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Mgt_login extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->service("Login_service");
    }

    //登入
    public function index(){
        $data = array(
            'login' => 1,
        );
        $this->load->view(LOGIN_PAGE,$data);
    }

    //登出
    public function logout(){
        $data = array(
            'login' => 0,
        );
        if($this->session->mgt_user_info){
            //清除token
            $this->deleteToken($this->session->mgt_user_info['token']);
        }
        //清除session
        if(isset($this->session->mgt_user_info)){
            $host = $this->common_service->get_ip();
            $this->common_service->logger("account:".$this->session->mgt_user_info['account'].",IP:".$host);
            $this->login_service->logout($this->session->mgt_user_info['account']);
        }
        $this->load->view(LOGIN_PAGE,$data);
    }
}
