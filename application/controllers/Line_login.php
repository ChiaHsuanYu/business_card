<?php
defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH. 'vendor/autoload.php';
Class Line_login extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url'); 
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->service("login_service");
    }
    public function login(){
        // 清除session
        if($this->session->userdata('line_access_token')){
            $this->session->unset_userdata('line_access_token');
        }
        $this->session->sess_destroy();
        if (isset($_GET["code"])) {            
            $result = $this->login_service->line_login($_GET["code"]);
            if($result['status']){
                header("Location: ".TOKEN_URL."?status=".$result['status']."&msg=&social_type=3&access_token=".$this->session->userdata('line_access_token'));
                exit(0);
            }
        }
        header("Location: ".TOKEN_URL."?status=".$result['status']."&msg=驗證失敗&social_type=3&access_token=");
    }
}