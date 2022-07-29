<?php
class User_msg_state_model extends CI_Model
{
    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 新增系統通知訊息狀態&對象
    public function add_user_msg_state($data){
        $sql = "INSERT INTO user_msg_state (MsgId, UserId) VALUES (?, ?)";
        $query = $this->db->query($sql,array($data['msgId'], $data['userId']));
        return $this->db->insert_id();
    }
}
