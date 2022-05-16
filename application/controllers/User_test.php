<?php
class User_test extends CI_Controller
{
    public function __construct() 
    {
  
        parent::__construct();
        $this->load->service('User_service');
    }
    public function login()
    {
        $name = 'phpddt.com';
        $psw = 'password';
        print_r($this->user_service->login($name, $psw));
    }
}