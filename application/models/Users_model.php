<?php
class Users_model extends CI_Model
{
    public $id = '';
    public $cellphone = '';
    public $password = '';
    public $verify = '';
    public $verifyCode = '';
    public $superID = '';
    public $name = '';
    public $avatar = '';
    public $createTime = '';
   
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
                $obj->cellphone = $row->Cellphone;
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
        $sql = "UPDATE users SET  Token = ?,  TokenCreateTime = ?, TokenUpdateTime = ? WHERE Id = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user_id, 0));
        return $query;
    }

    //登入驗證
    public function get_user_by_telpwd($cellphone, $password){
        $password = md5($password);
        $sql = "SELECT users.* FROM users WHERE users.Cellphone=? AND users.Password=? AND users.Verify=1 AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($cellphone, $password));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->cellphone = $row->Cellphone;
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
}
