<?php
require APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends BaseController {

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
    public function index()
    {
        $data = array(
            'account' => $this->security->xss_clean($this->input->post("account")),
            'superID' => $this->security->xss_clean($this->input->post("superID")),
            'company' => $this->security->xss_clean($this->input->post("company")),
            'industryId' => $this->security->xss_clean($this->input->post("industryId")),
            'startDT' => $this->security->xss_clean($this->input->post("startDT")),
            'endDT' => $this->security->xss_clean($this->input->post("endDT")),
            'page' => $this->security->xss_clean($this->input->post("page")),
            'page_count' =>$this->security->xss_clean($this->input->post("page_count")),
            'title' => '用戶管理'
        );
        $this->load->view('templates/header',$data);
        $this->load->view('users/index',$data);
        $this->load->view('templates/footer');
    }

    // // 新增帳號
    // public function add()
    // {
    //     $data = array(
    //         'title' => '新增帳號'
    //     );
    //     $this->load->view('templates/header',$data);
    //     $this->load->view('users/add_acc',$data);
    //     $this->load->view('templates/footer');
    // }

    // // 修改帳號
    // public function edit(){
    //     $data = array(
    //         'id' => $this->security->xss_clean($this->input->post("id")),
    //         'areaId' => $this->security->xss_clean($this->input->post("areaId")),
    //         'groupsId' => $this->security->xss_clean($this->input->post("groupsId")),
    //         'account' => $this->security->xss_clean($this->input->post("account")),
    //         'page' => $this->security->xss_clean($this->input->post("page")),
    //         'page_count' =>$this->security->xss_clean($this->input->post("page_count")),
    //         'title' => '修改帳號'
    //     );
    //     $this->load->view('templates/header',$data);
    //     $this->load->view('users/edit_pass');
    //     $this->load->view('users/edit_acc',$data);
    //     $this->load->view('templates/footer');
    // }

    // // 帳號詳細資料
    // public function detail(){
    //     $data = array(
    //         'id' => $this->security->xss_clean($this->input->post("id")),
    //         'areaId' => $this->security->xss_clean($this->input->post("areaId")),
    //         'groupsId' => $this->security->xss_clean($this->input->post("groupsId")),
    //         'account' => $this->security->xss_clean($this->input->post("account")),
    //         'page' => $this->security->xss_clean($this->input->post("page")),
    //         'page_count' =>$this->security->xss_clean($this->input->post("page_count")),
    //         'title' => '檢視帳號'
    //     );
    //     $this->load->view('templates/header',$data);
    //     $this->load->view('users/detail',$data);
    //     $this->load->view('templates/footer');
    // }
}