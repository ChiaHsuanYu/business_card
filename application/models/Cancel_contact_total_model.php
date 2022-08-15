<?php
class Cancel_contact_total_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 檢查特定名單是否存在
    public function check_cancel_contact_total($data){
        $sql = "SELECT Id FROM cancel_contact_total WHERE `UserId` = ? AND Contact_userId = ?;";
        $query = $this->db->query($sql, array($data['userId'],$data['contact_userId']));
        return $query->num_rows();
    }

    // 新增取消接觸時間統計名單
    public function add_cancel_contact_total($data){
        $sql = "INSERT INTO cancel_contact_total (UserId, Contact_userId) VALUES (?, ?)";
        $query = $this->db->query($sql,array($data['userId'], $data['contact_userId']));
        return $this->db->insert_id();
    }
}
