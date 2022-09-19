<?php
require_once APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Mgt_sys_msg extends BaseController {

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
            'title' => $this->security->xss_clean($this->input->post("title")),
            'msg' => $this->security->xss_clean($this->input->post("msg")),
            'title' => '新增系統通知訊息'
        );
        $this->load->view('templates/header',$data);
        $this->load->view('mgt_sys_msg/model');
        $this->load->view('mgt_sys_msg/add',$data);
        $this->load->view('templates/footer');
    }

    public function index(){
        $data = array(
            'title' => '系統通知訊息維護',
            'msg' => $this->security->xss_clean($this->input->post("msg")),
            'msg_title' => $this->security->xss_clean($this->input->post("msg_title")),
            'page' => $this->security->xss_clean($this->input->post("page")),
            'page_count' => $this->security->xss_clean($this->input->post("page_count")),
        );
        $this->load->view('templates/header',$data);
        $this->load->view('mgt_sys_msg/model');
        $this->load->view('mgt_sys_msg/index',$data);
        $this->load->view('templates/footer');
    }

    public function edit($id){
        $data = array(
            'id' => $id,
            'title' => '修改系統通知訊息'
        );
        $this->load->view('templates/header',$data);
        $this->load->view('mgt_sys_msg/model');
        $this->load->view('mgt_sys_msg/edit',$data);
        $this->load->view('templates/footer');
    }
}