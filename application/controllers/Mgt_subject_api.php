<?php

require APPPATH . 'controllers/BaseAPIController.php';
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Mgt_subject_api extends BaseAPIController 
{
    //連接指定的model檔案 
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("form_validation"));
        $this->load->helper("security");
        $this->load->service("mgt_subject_service");
        $this->load->service('common_service');
        $this->load->library('session');

        // 登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){             //Token合法並具有權限，將資料儲存在session           
            $this->session->mgt_user_info = (array)$r['data'];   
        }else{                              //Token不合法或逾時，讓使用者執行登出
            $this->response($r,401); // REST_Controller::HTTP_OK     
            exit("Invalid Token");
        }
    }

    // 新增主題
    public function add_subject_post(){   
        $data = array(
            "imageURL" => $this->security->xss_clean($this->input->post("imageURL")),
            "subjectFile" => $this->security->xss_clean($this->input->post("subjectFile")),
            "name" => $this->security->xss_clean($this->input->post("name"))
        );
        $this->form_validation->set_rules("name", "lang:「主題名稱」","trim|required");
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => 0,
                "msg" => $this->form_validation->error_string()
            ); 
            $this->response($result,200); // REST_Controller::HTTP_NOT_FOUND
        }else{
            //檢查個人圖像並上傳
            $data['image_path'] = null;
            if (!empty($_FILES['imageURL']["tmp_name"])) {
                $path = SUBJECT_IMAGE_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                //重新命名
                $uuid = $this->common_service->create_uuid($_FILES['imageURL']['name']); //取得UUID
                $fileName = $_FILES['imageURL']['name'];
                $name = explode('.', $fileName);
                $newName = $uuid . '.' . $name[1];
                $config['upload_path']= $path;//上傳路徑
                $config['allowed_types']= 'jpg|jpeg|png|gif|svg';//檔案限制類型
                // $config['max_size'] = '5120'; //限制檔案上傳大小
                // $config['max_width'] = '1024'; //上傳圖片的寬度最大值
                // $config['max_height'] = '768'; //上傳圖片的高度最大值
                $config['file_name'] = $newName;
                $this->load->library('upload', $config);
                // 判斷是否上傳成功
                if ( !$this->upload->do_upload('imageURL')){  
                        $result['status'] = 2;
                        $result['msg'] = $this->upload->display_errors();
                        $this->response($result,200);//上傳檔案失敗訊息
                }else{   
                    $result= array('upload_data' => $this->upload->data()); 
                    $data['image_path']=$result['upload_data']['orig_name'];
                }
            }else{
                $result = array(
                    "status" => 0,
                    "msg" => "請選擇主題縮圖"
                ); 
                $this->response($result,200); 
            }
            //檢查CSS檔案並上傳
            $data['css_path'] = null;
            if (!empty($_FILES['subjectFile']["tmp_name"])) {
                $path = SUBJECT_CSS_PATH;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                //重新命名
                $uuid = $this->common_service->create_uuid($_FILES['subjectFile']['name']); //取得UUID
                // $_FILES['subjectFile']['name']
                if (file_exists(SUBJECT_CSS_PATH.$_FILES['subjectFile']['name'])) {
                    $result = array(
                        "status" => 0,
                        "msg" => "檔名已存在，請重新命名"
                    ); 
                    $this->response($result,200); 
                }
                $config['upload_path']= $path;//上傳路徑
                $config['allowed_types']= 'css';//檔案限制類型
                // $config['max_size'] = '5120'; //限制檔案上傳大小
                // $config['max_width'] = '1024'; //上傳圖片的寬度最大值
                // $config['max_height'] = '768'; //上傳圖片的高度最大值
                $config['file_name'] = $_FILES['subjectFile']['name'];
                $this->upload->initialize($config); //調用初始化函數initialize,加載新的配置
                // 判斷是否上傳成功
                if ( !$this->upload->do_upload('subjectFile')){  
                        $result['status'] = 3;
                        $result['msg'] = $this->upload->display_errors();
                        $this->response($result,200);//上傳檔案失敗訊息
                }else{   
                    $result= array('upload_data' => $this->upload->data()); 
                    $data['css_path']=$result['upload_data']['orig_name'];
                }
            }else{
                $result = array(
                    "status" => 0,
                    "msg" => "請選擇CSS檔案"
                ); 
                $this->response($result,200); 
            }
            $this->response($this->mgt_subject_service->add_subject($data),200); // REST_Controller::HTTP_OK
        }
    }
}