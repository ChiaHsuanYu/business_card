<?php

require APPPATH . 'controllers/BaseAPIController_1.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Users_test_api extends BaseAPIController_1 
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
        // $r = $this->checkAA_front();
        // if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
        //     $this->session->user_info = (array)$r['data'];   
        // }else{                              //Token不合法或逾時，讓使用者執行登出
        //     $this->response($r,401); // REST_Controller::HTTP_OK     
        //     exit("Invalid Token");
        // }
    }

    // 取得使用者資料 by superId,method:POST
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
            // header("Access-Control-Allow-Origin: https://192.168.88.123");
            // $this->response( $device,200); // REST_Controller::HTTP_OK     
            // $mobile = $this->isMobile();  
            // $device = '0'; 
            // if($mobile){
            //     $device = '1';
            // }
            $this->response( $this->users_service->get_user_by_superId($data),200); // REST_Controller::HTTP_OK     
        }
    }

    // 取得使用者資料 by superId,method:GET
    public function get_user_by_superId_get($superId){   
        if(empty($superId)){
            $result = array(
                "status" => 0,
                "message" => "superID不可為空"
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $data = array(
                "superId" => $superId
            );
            $this->response($this->users_service->get_user_by_superId($data),200); // REST_Controller::HTTP_OK     
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
    public function edit_personal_user_post(){   
        $data = array(
            "personal_superID" => $this->security->xss_clean($this->input->post("personal_superID")),
            "personal_name" => $this->security->xss_clean($this->input->post("personal_name")),
            "personal_nickname" => $this->security->xss_clean($this->input->post("personal_nickname")),
            "personal_avatar" => $this->security->xss_clean($this->input->post("personal_avatar")),
            "personal_avatar_id" => $this->security->xss_clean($this->input->post("personal_avatar_id")),
            "company_name" => $this->security->xss_clean($this->input->post("company_name")),
            "company_position" => $this->security->xss_clean($this->input->post("company_position")),
            "company_logo" => $this->security->xss_clean($this->input->post("company_logo")),
        );
        $this->form_validation->set_rules('personal_superID', 'lang:「SUPERID」', 'required');
        $this->form_validation->set_rules('personal_name', 'lang:「姓名」', 'required|max_length[20]');
        $this->form_validation->set_rules('company_name', 'lang:「公司名稱」', 'required');
        $this->form_validation->set_rules('company_position', 'lang:「公司職位」', 'required');
        if($data['personal_nickname']){
            $this->form_validation->set_rules('personal_nickname', 'lang:「暱稱」', 'required|max_length[20]');
        }

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            // $data['personal_avatar_path'] = $_FILES['personal_avatar']["tmp_name"];
            // $data['company_logo_path'] = $_FILES['company_logo']["tmp_name"];
            $data['personal_avatar_path'] = null;
            $data['company_logo_path'] = null;
            $config_status = null;
            //檢查有沒有個人圖像
            if (!empty($_FILES['personal_avatar']["tmp_name"])) {
                // $data['personal_avatar_path'] = $_FILES['personal_avatar']["tmp_name"];
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
                $config['allowed_types']= 'jpg|jpeg|png|gif|svg';//檔案限制類型
                $config['max_size'] = '5120'; //限制檔案上傳大小
                // $config['max_width'] = '1024'; //上傳圖片的寬度最大值
                // $config['max_height'] = '768'; //上傳圖片的高度最大值
                $config['file_name'] = $newName;
                $this->load->library('upload', $config);
                $config_status = 1;
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
            // //檢查有沒有公司LOGO
            if (!empty($_FILES['company_logo']["tmp_name"])) {
                // $data['company_logo_path'] = $_FILES['company_logo']["tmp_name"];
                $path = LOGO_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                //重新命名
                $uuid = $this->common_service->create_uuid(); //取得UUID
                $fileName = $_FILES['company_logo']['name']; 
                $name = explode('.', $fileName);
                $newName = $uuid . '.' . $name[1];
                $config['upload_path']= $path;
                $config['allowed_types']= 'jpg|jpeg|png';//檔案限制類型
                $config['max_size'] = '5120'; 
                // $config['max_width'] = '1024';
                // $config['max_height'] = '768';
                $config['file_name'] = $newName;
                if($config_status){
                    $this->upload->initialize($config); //調用初始化函數initialize,加載新的配置
                }else{
                    $config_status = 1;
                    $this->load->library('upload', $config);
                }
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
            // $this->response($data,200); // REST_Controller::HTTP_OK     
            $this->response( $this->users_service->edit_personal_acc($data),200); // REST_Controller::HTTP_OK     
        }
    }

    //編輯個人檔案 by userId
    public function update_acc_by_id_post(){
        $data = array(
            "personal_avatar" => $this->security->xss_clean($this->input->post("personal_avatar")),
            "personal_avatar_id" => $this->security->xss_clean($this->input->post("personal_avatar_id")),
            "userInfo" => $this->security->xss_clean($this->input->post("userInfo")),
        );
        $data['userInfo'] = json_decode($data['userInfo'],0);
        for ($i=0;$i<count($data['userInfo']->companyInfo);$i++) {
            $data['company_logo_'.$i] = $this->security->xss_clean($this->input->post("company_logo_".$i));
        }
        $this->form_validation->set_rules('userInfo', 'lang:「個人資料」', 'required');
        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $data['userInfo']->personal_avatar_path = null;
            $config_status = null;
            //檢查個人圖像並上傳
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
                $config['allowed_types']= 'jpg|jpeg|png|gif|svg';//檔案限制類型
                $config['max_size'] = '5120'; //限制檔案上傳大小
                // $config['max_width'] = '1024'; //上傳圖片的寬度最大值
                // $config['max_height'] = '768'; //上傳圖片的高度最大值
                $config['file_name'] = $newName;
                $this->load->library('upload', $config);
                $config_status = 1;
                // 判斷是否上傳成功
                if ( !$this->upload->do_upload('personal_avatar')){  
                        $result['status'] = 0;
                        $result['message'] = $this->upload->display_errors();
                        $this->response($result,200);//上傳檔案失敗訊息
                }else{   
                    $result= array('upload_data' => $this->upload->data()); 
                    $data['userInfo']->personal_avatar_path=$result['upload_data']['orig_name'];
                }
            }
            // 依序檢查公司LOGO並上傳
            $companyInfo = $data['userInfo']->companyInfo;
            for($i=0;$i<count($companyInfo);$i++){
                $logo_id = 'company_logo_'.($i+1);
                if (!empty($_FILES[$logo_id]['tmp_name'])) {
                    $path = LOGO_PATH;
                    if (!is_dir($path)) {
                        mkdir($path, 0755);
                    }
                    //重新命名
                    $uuid = $this->common_service->create_uuid(); //取得UUID
                    $fileName = $_FILES[$logo_id]['name'];
                    $name = explode('.', $fileName);
                    $newName = $uuid . '.' . $name[1];
                    $config['upload_path']= $path;//上傳路徑
                    $config['allowed_types']= 'jpg|jpeg|png';//檔案限制類型
                    $config['max_size'] = '5120'; 
                    // $config['max_width'] = '1024';
                    // $config['max_height'] = '768';
                    $config['file_name'] = $newName;
                    if($config_status){
                        $this->upload->initialize($config); //調用初始化函數initialize,加載新的配置
                    }else{
                        $config_status = 1;
                        $this->load->library('upload', $config);
                    }

                    // 判斷是否上傳成功
                    if ( ! $this->upload->do_upload("$logo_id")){     
                        $result['file']= $this->upload->display_errors();
                        //沒選擇檔案
                        if($result['file']=="<p>尚未選擇上傳檔案</p>"){
                            $companyInfo[$i]->company_logo_path = null;
                        }else{
                            $result['status']=0;
                            $result['message']=$result['file'];
                            $this->response($result,200);//檔案新增失敗訊息
                            return false;
                        }   
                    }else{
                        $result= array('upload_data' => $this->upload->data());
                        $companyInfo[$i]->company_logo_path=$result['upload_data']['orig_name'];
                    }
                }else{
                    $companyInfo[$i]->company_logo_path = null;
                }
            }
            $data['userInfo']->companyInfo = $companyInfo;
            $data['userInfo']->personal_avatar_id = $data['personal_avatar_id'];
            $this->response($this->users_service->update_acc_by_id($data['userInfo']),200); // REST_Controller::HTTP_OK     
        }
    }

    // 更改使用者主題 by userId
    public function update_subjectId_by_id_post(){   
        $data = array(
            "subjectId" => $this->security->xss_clean($this->input->post("subjectId"))
        );
        $this->form_validation->set_rules("subjectId", "lang:「主題ID」","trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->update_subjectId_by_id($data),200); // REST_Controller::HTTP_OK
        }
    }
    
    //修改SUPER ID by userId
    public function update_superId_by_id_post(){
        $data = array(
            "superId" => $this->security->xss_clean($this->input->post("superId")),
        );
        $this->form_validation->set_rules('superId', 'lang:「SUPERID」', 'required|min_length[6]|max_length[15]');
        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "message" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            $this->response($this->users_service->update_superId_by_id($data),200); // REST_Controller::HTTP_OK     
        }
    }
}