<?php
defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH. 'vendor/autoload.php';
class Facebook_login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url'); 
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->service("login_service");
        $this->load->library('facebook'); 
    }

    public function login(){
        if (isset($_GET["code"])) {            
            $result = $this->login_service->facebook_login($_GET["code"]);
            if($result['status']){
                header("Location: ".TOKEN_URL."?status=".$result['status']."&msg=&social_type=2&access_token=".$this->session->userdata('fb_access_token'));
                exit(0);
            }
        }
        header("Location: ".TOKEN_URL."?status=".$result['status']."&msg=驗證失敗&social_type=2&access_token=");
    }

    public function logout(){
        $this->facebook->destroy_session(); 
        $this->session->unset_userdata('fb_access_token');
        redirect('user_authentication'); 
    }
}
