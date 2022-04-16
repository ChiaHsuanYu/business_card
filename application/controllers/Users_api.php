<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Users_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("users_service");
        $this->load->service('common_service');
        $this->load->library('session');

        // 登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->user_info = (array)$r['data'];       
        }else{                              //Token不合法或逾時，讓使用者執行登出
            exit("Invalid Token");
        }
    }

    // 取得使用者資料 by superId
    public function get_user_by_superId_post(){   
        $data = array(
            "superId" => $this->security->xss_clean($this->input->post("superId")),
        );
        $this->form_validation->set_rules('superId', 'lang:「superID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->users_service->get_user_by_superId($data),200); // REST_Controller::HTTP_OK     
        }
    }

    // 檢查SUPER ID是否重複
    public function check_superId_post(){   
        $data = array(
            "superId" => $this->security->xss_clean($this->input->post("superId")),
        );
        $this->form_validation->set_rules('superId', 'lang:「superID」', 'required');
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response( $this->users_service->check_superId($data),200); // REST_Controller::HTTP_OK     
        }
    }

    // 修改基本資料
    public function edit_personal_acc_post(){   
        $data = array(
            "personal_superID" => $this->security->xss_clean($this->input->post("personal_superID")),
            "personal_name" => $this->security->xss_clean($this->input->post("personal_name")),
            "personal_nickname" => $this->security->xss_clean($this->input->post("personal_nickname")),
            "personal_avatar" => $this->security->xss_clean($this->input->post("personal_avatar")),
            "company_name" => $this->security->xss_clean($this->input->post("company_name")),
            "company_position" => $this->security->xss_clean($this->input->post("company_position")),
            "company_logo" => $this->security->xss_clean($this->input->post("company_logo")),
        );
        $this->form_validation->set_rules('personal_superID', 'lang:「SUPERID」', 'required|min_length[6]|max_length[15]');
        $this->form_validation->set_rules('personal_name', 'lang:「姓名」', 'required');
        $this->form_validation->set_rules('company_name', 'lang:「公司名稱」', 'required');
        $this->form_validation->set_rules('company_position', 'lang:「公司職位」', 'required');

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $data['personal_avatar_path'] = null;
            $data['company_logo_path'] = null;
            //檢查有沒有個人圖像
            if (!empty($_FILES['personal_avatar']["tmp_name"])) {
                $path = AVATAR_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                //重新命名
                $uuid = $this->common_service->create_uuid(); //取得UUID
                $fileName = $_FILES['personal_avatar']['name'];
                $name = explode('.', $fileName);
                $newName = $uuid . '.' . $name[1];
                $config['upload_path']= $path;//上傳路徑
                $config['allowed_types']= 'gif|jpg|png|svg';//檔案限制類型
                $config['max_size'] = '2048'; //限制檔案上傳大小
                $config['max_width'] = '1024'; //上傳圖片的寬度最大值
                $config['max_height'] = '768'; //上傳圖片的高度最大值
                $config['file_name'] = $newName;
                $this->load->library('upload', $config);
                // 判斷是否上傳成功
                if ( !$this->upload->do_upload('personal_avatar')){  
                        $result['status'] = 0;
                        $result['message'] = $this->upload->display_errors();
                        $this->response($result,200);//上傳檔案失敗訊息
                }else{   
                    $result= array('upload_data' => $this->upload->data()); 
                    $data['personal_avatar_path']=$result['upload_data']['orig_name'];
                }
            }
            //檢查有沒有公司LOGO
            if (!empty($_FILES['company_logo']["tmp_name"])) {
                $path = LOGO_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                //重新命名
                $uuid = $this->common_service->create_uuid(); //取得UUID
                $fileName = $_FILES['company_logo']['name']; 
                $name = explode('.', $fileName);
                $newName = $uuid . '.' . $name[1];
                $config2['upload_path']= $path;
                $config2['allowed_types']= 'gif|jpg|png|svg';
                $config2['max_size'] = '2048'; 
                $config2['max_width'] = '1024';
                $config2['max_height'] = '768';
                $config2['file_name'] = $newName;
                $this->upload->initialize($config2); //調用初始化函數initialize,加載新的配置
                // 判斷是否上傳成功
                if ( !$this->upload->do_upload('company_logo')){  
                        $result['status'] = 0;
                        $result['message'] = $this->upload->display_errors();
                        $this->response($result,200);//上傳檔案失敗訊息
                }else{   
                    $result= array('upload_data' => $this->upload->data()); 
                    $data['company_logo_path']=$result['upload_data']['orig_name'];
                }
            }
            $this->response( $this->users_service->edit_personal_acc($data),200); // REST_Controller::HTTP_OK     
        }
    }

    // 修改密碼
    public function update_password_post(){   
        $data = array(
            "password_old" => $this->security->xss_clean($this->input->post("password_old")),
            "password_new" => $this->security->xss_clean($this->input->post("password_new"))
        );
        $this->form_validation->set_rules("password_old", "lang:「舊密碼」", "trim|required");
        $this->form_validation->set_rules("password_new", "lang:「新密碼」","trim|required|min_length[6]|max_length[20]|callback_check_string_validation");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->update_password($data),200); // REST_Controller::HTTP_OK
        }
    }

    //編輯個人檔案
    public function update_acc_by_id_post(){
        $data = array(
            "order" => $this->security->xss_clean($this->input->post("order")),
            "personal_superID" => $this->security->xss_clean($this->input->post("personal_superID")),
            "personal_name" => $this->security->xss_clean($this->input->post("personal_name")),
            "personal_avatar" => $this->security->xss_clean($this->input->post("personal_avatar")),
            "personal_nickname" => $this->security->xss_clean($this->input->post("personal_nickname")),
            "personal_email" => $this->security->xss_clean($this->input->post("personal_email")),
            "personal_phone" => $this->security->xss_clean($this->input->post("personal_phone")),
            "personal_social" => $this->security->xss_clean($this->input->post("personal_social")),
            "companyInfo" => $this->security->xss_clean($this->input->post("companyInfo")),
        );
        $this->form_validation->set_rules('personal_superID', 'lang:「SUPERID」', 'required');
        $this->form_validation->set_rules('personal_name', 'lang:「姓名」', 'required');
        $this->form_validation->set_rules('companyInfo', 'lang:「公司資訊」', 'required');
        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->update_acc_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }
}