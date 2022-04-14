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
   
    // 連接資料庫
    public function __construct(){
        $this->load->database();
    }

    // 查詢使用者token是否存在
    public function get_user_by_token($token){
        $sql = "SELECT users.* FROM users WHERE users.token = ? AND users.isDeleted = ?";
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
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token為NULL
    public function update_Token_as_NULL($token){
        $sql = "UPDATE users SET Token = NULL WHERE Token = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($token, 0));
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

    //登入驗證
    public function get_user_by_accpwd($account, $password){
        $password = md5($password);
        $sql = "SELECT users.* FROM users WHERE users.Account=? AND users.Password=? AND users.Verify=1 AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($account, $password));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->verify = $row->Verify;
                $obj->verifyCode = $row->VerifyCode;
                $obj->superID = $row->SuperID;
                $obj->name = $row->Name;
                $obj->avatar = $row->Avatar;
                $obj->createTime = $row->CreateTime;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查手機號碼是否存在
    public function check_account($account){
        $sql = "SELECT Id,Account,Verify,`Password` FROM users WHERE Account=? AND isDeleted = 0";
        $query = $this->db->query($sql, array($account));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->verify = $row->Verify;
                $obj->password = $row->Password;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查使用者驗證狀態
    public function check_account_by_id($id){
        $sql = "SELECT Id,Account,Verify,`Password` FROM users WHERE Id=? AND isDeleted = 0";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->verify = $row->Verify;
                $obj->password = $row->Password;
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
        $sql = "SELECT Id,Verify FROM users WHERE Id=? AND VerifyCode=?  AND isDeleted = 0";
        $query = $this->db->query($sql, array($data['userId'],$data['vaild']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->verify = $row->Verify;
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

    // 設定密碼 by id
    public function update_password_by_id($data){
        $password = md5($data['password']);
        $sql = "UPDATE users SET `Password` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($password,$data['id']));
        return $query;
    }
}
