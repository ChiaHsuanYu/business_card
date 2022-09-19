<?php
require APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_setting extends BaseController {

    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        //登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session            
            $this->session->mgt_user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            redirect(LOGOUT_PAGE);
        }
    }
    public function edit(){
        $data = array(
            'title' => '親密度累積設定'
        );
        $this->load->view('templates/header',$data);
        $this->load->view('contact_setting/model');
        $this->load->view('contact_setting/edit',$data);
        $this->load->view('templates/footer');
    }
}