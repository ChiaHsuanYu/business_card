<?php
class Contact_time_total_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }
    // 新增接觸時間累積
    public function add_contact_time_total($data){
        $sql = "INSERT INTO contact_time_total (UserId, Contact_userId, Contact_time, `Date`) VALUES (?, ?, ?, ?)";
        $query = $this->db->query($sql,array($data['userId'], $data['other_id'], $data['contact_time'], $data['date']));
        return $this->db->insert_id();
    }
    // 修改接觸時間累積
    public function update_contact_by_id($data){
        $sql = "UPDATE contact_time_total SET `Contact_time` = ?,ModifiedTime = ? WHERE Id = ?";
        $query = $this->db->query($sql,array($data['contact_time'],date('Y-m-d H:i:s'),$data['id']));
        return $query;
    }

    public function get_contact_cap_data($data){
        $sql = "SELECT UserId,Contact_userId FROM contact_time_total WHERE `Date` BETWEEN ? AND ? GROUP BY UserId,Contact_userId having COUNT(Id) >= ?;";
        $query = $this->db->query($sql, array($data['startDate'],$data['endDate'],CONTACT_CAP));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Contact_time_total_model();
                $obj->userId = $row->UserId;
                $obj->contact_userId = $row->Contact_userId;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
