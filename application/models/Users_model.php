<?php
class Users_model extends CI_Model
{
    public $id = '';
    public $account = '';
    public $order = '';
    public $companyOrder = '';
    public $personal_superID = '';
    public $personal_name = '';
    public $personal_avatar = '';
    public $personal_nickname = '';
    public $personal_email = '';
    public $personal_phone = '';
    public $personal_social = '';
    public $personal_subjectId = '';

    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 查詢使用者token是否存在
    public function get_user_by_token($token){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.token = ? AND users.isDeleted = ?";
        $query = $this->db->query($sql, array($token, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Email){
                    $obj->personal_email = explode(',',$row->Email);
                }
                if($row->Phone){
                    $obj->personal_phone = explode(',',$row->Phone);
                }
                if($row->Avatar){
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token為NULL
    public function update_Token_as_NULL($id){
        $sql = "UPDATE users SET Token = NULL, TokenCreateTime = NULL, TokenUpdateTime = NULL WHERE Id = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($id, 0));
        return $query;
    }

    // 更新使用者token的T_UpdateDT
    public function update_TUpdateDT_by_token($token){
        $sql = "UPDATE users SET TokenUpdateTime = ? WHERE Token = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $token, 0));
        return $query;
    }

    // 更新使用者token
    public function update_Token_by_id($user_id, $token){
        $sql = "UPDATE users SET Token = ?,  TokenCreateTime = ?, TokenUpdateTime = ? WHERE Id = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user_id, 0));
        return $query;
    }

    public function get_user_by_acc($account){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Account=?";
        $query = $this->db->query($sql, array($account));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Email){
                    $obj->personal_email = explode(',',$row->Email);
                }
                if($row->Phone){
                    $obj->personal_phone = explode(',',$row->Phone);
                }
                if($row->Avatar){
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //登入驗證(管理人員登入用)
    // public function get_user_by_accpwd($account, $password){
    //     $password = md5($password);
    //     $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
    //             LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Account=? AND users.Password=? AND users.Verify=1 AND users.isDeleted = 0";
    //     $query = $this->db->query($sql, array($account, $password));
    //     $result = array();
    //     if ($query->num_rows() > 0) {
    //         foreach ($query->result() as $row) {
    //             $obj = new Users_model();
    //             $obj->id = $row->Id;
    //             $obj->account = $row->Account;
    //             $obj->verify = $row->Verify;
    //             $obj->verifyCode = $row->VerifyCode;
    //             $obj->superID = $row->SuperID;
    //             $obj->name = $row->Name;
    //             $obj->avatar = $row->Avatar;
    //             $obj->subjectId = $row->SubjectId;
    //             $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
    //             $obj->subject_name = $row->subjectName;
    //             $obj->createTime = $row->CreateTime;
    //             $obj->token = $row->Token;
    //             $obj->tokenCreateTime = $row->TokenCreateTime;
    //             $obj->tokenUpdateTime = $row->TokenUpdateTime;
    //             array_push($result, $obj);
    //         }
    //     }
    //     return $result;
    // }

    // 檢查手機號碼是否存在
    public function check_account($account){
        $sql = "SELECT Id,Account,Verify FROM users WHERE Account=? AND isDeleted = 0";
        $query = $this->db->query($sql, array($account));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->verify = $row->Verify;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增使用者
    public function add_user($account){
        $sql = "INSERT INTO users (Account, CreateTime) VALUES (?, ?)";
        $query = $this->db->query($sql,array($account,date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 帳號驗證
    public function check_verify_by_id($data){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Id=? AND VerifyCode=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($data['userId'],$data['vaild']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Email){
                    $obj->personal_email = explode(',',$row->Email);
                }
                if($row->Phone){
                    $obj->personal_phone = explode(',',$row->Phone);
                }
                if($row->Avatar){
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }
    
    // 更改驗證碼 by id
    public function update_verifyCode_by_id($verifyCode,$id){
        $sql = "UPDATE users SET VerifyCode = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($verifyCode,$id));
        return $query;
    }

    // 更改驗證狀態 by id
    public function update_verify_by_id($id){
        $sql = "UPDATE users SET Verify = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array('1',$id));
        return $query;
    }

    // 取得使用者資料 by superId
    public function get_user_by_superId($data){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.SuperID=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($data['superId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_name = $row->subjectName;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Email){
                    $obj->personal_email = explode(',',$row->Email);
                }
                if($row->Phone){
                    $obj->personal_phone = explode(',',$row->Phone);
                }
                if($row->Avatar){
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查SUPER ID是否重複
    public function check_superId($data){
        $sql = "SELECT Id FROM users WHERE Id != ? AND SuperID = ? AND isDeleted = 0";
        $query = $this->db->query($sql, array($data['id'],$data['superId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 修改基本資料 by id
    public function update_personal_by_id($data){
        $sql = "UPDATE users SET `SuperID` = ?,`Name` = ?,`Nickname` = ?,`Avatar` = ?,`CompanyOrder` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($data['personal_superID'],$data['personal_name'],$data['personal_nickname'],$data['personal_avatar_path'],$data['companyOrder'],$data['id']));
        return $query;
    }
}
