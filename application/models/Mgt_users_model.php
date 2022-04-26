<?php
class Mgt_users_model extends CI_Model
{
    public $id = '';
    public $account = '';
    public $companyOrder = '';
    public $name = '';
    public $email = '';
    public $phone = '';

    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 查詢使用者token是否存在
    public function get_user_by_token($token){
        $sql = "SELECT mgt_users.* FROM mgt_users WHERE mgt_users.token = ? AND mgt_users.isDeleted = ?";
        $query = $this->db->query($sql, array($token, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Mgt_users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->name = $row->Name;
                $obj->email = $row->Email;
                $obj->phone = $row->Phone;
                $obj->isDeleted = $row->isDeleted;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token為NULL
    public function update_Token_as_NULL($token){
        $sql = "UPDATE mgt_users SET Token = NULL, TokenCreateTime = NULL, TokenUpdateTime = NULL WHERE Token = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($token, 0));
        return $query;
    }

    // 更新使用者token的T_UpdateDT
    public function update_TUpdateDT_by_token($token){
        $sql = "UPDATE mgt_users SET TokenUpdateTime = ? WHERE Token = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $token, 0));
        return $query;
    }

    // 更新使用者token
    public function update_Token_by_id($user_id, $token){
        $sql = "UPDATE mgt_users SET Token = ?,  TokenCreateTime = ?, TokenUpdateTime = ? WHERE Id = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user_id, 0));
        return $query;
    }

    //登入驗證
    public function get_user_by_accpwd($account, $password){
        $password = md5($password);
        $sql = "SELECT mgt_users.* FROM mgt_users WHERE mgt_users.Account=? AND mgt_users.Password=? AND mgt_users.isDeleted = 0";
        $query = $this->db->query($sql, array($account, $password));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Mgt_users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->name = $row->Name;
                $obj->phone = $row->Phone;
                $obj->email = $row->Email;
                $obj->createTime = $row->CreateTime;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查使用者舊密碼
    public function check_user_by_password($data){
        $data['password_old'] = md5($data['password_old']);
        $sql = "SELECT Id FROM mgt_users WHERE Id = ? AND `Password` = ? AND isDeleted = 0";
        $query = $this->db->query($sql, array($data['id'],$data['password_old']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Mgt_users_model();
                $obj->id = $row->Id;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者新密碼 by id
    public function update_password_by_id($data){
        $data['password'] = md5($data['password']);
        $sql = "UPDATE mgt_users SET `Password` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($data['password'],$data['id']));
        return $query;
    }
}
