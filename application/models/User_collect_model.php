<?php
class User_collect_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }
    // 檢查是否已有收藏紀錄
    public function check_user_collect($data){
        $sql = "SELECT Id,isCollected FROM `user_collect` WHERE UserId = ? AND Collect_userId = ? AND isCollected != '0'";
        $query = $this->db->query($sql, array($data['userId'],$data['collect_userId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new User_collect_model();
                $obj->id = $row->Id;
                $obj->isCollected = $row->isCollected;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增收藏紀錄
    public function add_user_collect($data){
        $sql = "INSERT INTO `user_collect` (UserId,Collect_userId,isCollected) VALUES (?,?,?)";
        $query = $this->db->query($sql,array($data['userId'],$data['collect_userId'],$data['isCollected']));
        return $this->db->insert_id();
    }
}
