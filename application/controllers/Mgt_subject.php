<?php
require APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Mgt_subject extends BaseController {

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
    public function add(){
        $data = array(
            'id' => $this->security->xss_clean($this->input->post("id")),
            'title' => '主題新增上傳'
        );
        $this->load->view('templates/header',$data);
        $this->load->view('mgt_subject/model');
        $this->load->view('mgt_subject/add',$data);
        $this->load->view('templates/footer');
    }

    public function index(){
        $data = array(
            'title' => '主題維護'
        );
        $this->load->view('templates/header',$data);
        $this->load->view('mgt_subject/model');
        $this->load->view('mgt_subject/index',$data);
        $this->load->view('templates/footer');
    }
}