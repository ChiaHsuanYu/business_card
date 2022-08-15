<?php
class Contact_setting_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得接觸條件設定
    public function get_contact_setting(){
        $sql = "SELECT * FROM contact_setting";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Contact_setting_model();
                $obj->id = $row->Id;
                $obj->distance = $row->Distance;
                $obj->max_contact_time = $row->Max_contact_time;
                $obj->min_contact_time = $row->Min_contact_time;
                array_push($result, $obj);
            }
        }
        return $result;
    }
    // 修改接觸條件設定
    public function update_contact_setting_by_id($data){
        $sql = "UPDATE contact_setting SET `Distance` = ?,Max_contact_time = ?,Min_contact_time = ?,ModifiedTime = ? WHERE Id = ?";
        $query = $this->db->query($sql,array($data['distance'],$data['max_contact_time'],$data['min_contact_time'],date('Y-m-d H:i:s'),$data['id']));
        return $query;
    }
}
