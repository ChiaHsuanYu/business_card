<?php
class Sms_log_model extends CI_Model
{
    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 新增簡訊發送紀錄
    public function add_sms_log($data){
        $sql = "INSERT INTO sms_log (MobileNumber, `Status`, Msg) VALUES (?, ?, ?)";
        $query = $this->db->query($sql,array($data['mobile_number'], $data['status'], $data['msg']));
        return $this->db->insert_id();
    }
}
