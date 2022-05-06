<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Google_login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url'); 
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->service("login_service");
    }
    public function login(){
        $this->session->unset_userdata('access_token');
        $this->session->unset_userdata('user_data');
        if (isset($_GET["code"])) {
            $code = $_GET["code"];
            if($code){
                $result = $this->login_service->google_login($code);
                if($result['status']){
                    header("Location: ".TOKEN_URL."?status=".$result['status']."&msg=&social_type=1&access_token=".$this->session->userdata('access_token'));
                    exit(0);
                }
            }
        }
        header("Location: ".TOKEN_URL."?status=".$result['status']."&msg=".$result['msg']."&social_type=1&access_token=");
    }

    public function logout(){
        $this->session->unset_userdata('access_token');
        $this->session->unset_userdata('user_data');

        redirect('google_login/login');
    }
}
