<?php
class Mgt_login_model extends CI_Model
{
    public $id = '';
    public $mgtUserId  = '';
    public $host = '';

    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 新增登入紀錄
    public function add_mgt_login($mgtUserId,$host){
        $sql = "INSERT INTO mgt_login (MgtUserId, `Host`) VALUES (?, ?)";
        $query = $this->db->query($sql,array($mgtUserId, $host));
        return $this->db->insert_id();
    }

    // 檢查使用者裝置是否已存在
    // public function check_login_by_userId($mgtUserId,$host){
    //     $sql = "SELECT mgt_login.* FROM mgt_login WHERE mgt_login.MgtUserId = ? AND mgt_login.Host = ?";
    //     $query = $this->db->query($sql, array($mgtUserId,$host));
    //     $result = array();
    //     if ($query->num_rows() > 0) {
    //         foreach ($query->result() as $row) {
    //             $obj = new Mgt_login_model();
    //             $obj->id = $row->Id;
    //             $obj->mgtUserId = $row->mgtUserId;
    //             $obj->host = $row->Host;
    //             $obj->lastTime = $row->LastTime;
    //             array_push($result, $obj);
    //         }
    //     }
    //     return $result;
    // }

    // // 更新最新登入時間
    // public function update_login_by_id($id){
    //     $sql = "UPDATE mgt_login SET LastTime = ? WHERE Id = ?";
    //     $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $id));
    //     return $query;
    // }
}