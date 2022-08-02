<?php
class Scan_record_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }
    // 檢查是否已有瀏覽紀錄
    public function check_scan_record($data){
        $sql = "SELECT Id FROM `scan_record` WHERE UserId = ? AND Scan_userId = ? AND isDeleted = '0'";
        $query = $this->db->query($sql, array($data['userId'],$data['scan_userId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Scan_record_model();
                $obj->id = $row->Id;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增瀏覽紀錄
    public function add_scan_record($data){
        $sql = "INSERT INTO `scan_record` (UserId,Scan_userId,ScanTime) VALUES (?,?,?)";
        $query = $this->db->query($sql,array($data['userId'],$data['scan_userId'],$data['scanTime']));
        return $this->db->insert_id();
    }
    
    // 修改瀏覽紀錄
    public function update_scan_record_by_id($data){
        $sql = "UPDATE scan_record SET ScanTime = ?, ModifiedTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array($data['scanTime'],date('Y-m-d H:i:s'),$data['id']));
        return $query;
    }

    // 查詢瀏覽紀錄
    public function get_scan_record($userId){
        $sql = "SELECT users.* FROM scan_record INNER JOIN `users` ON scan_record.Scan_userId = users.Id 
                WHERE scan_record.UserId = ? AND scan_record.isDeleted = 0 AND users.isDeleted = 0 GROUP BY users.Id ORDER BY scan_record.ScanTime DESC";
        $query = $this->db->query($sql, array($userId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Scan_record_model();
                $obj->id = $row->Id;
                $obj->companyOrder = $row->CompanyOrder;
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // query_scan_record_by_userId
}
