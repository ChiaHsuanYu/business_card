<?php
class Sys_msg_model extends CI_Model
{
    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 新增系統通知訊息
    public function add_sys_msg($data){
        if(!isset($data['userId'])){
            $data['userId'] = NULL;
        }
        $sql = "INSERT INTO sys_msg (Title, Msg, UserId) VALUES (?, ?, ?)";
        $query = $this->db->query($sql,array($data['title'], $data['msg'], $data['userId']));
        return $this->db->insert_id();
    }

    // 修改系統通知訊息
    public function update_sys_msg_by_id($data){
        $sql = "UPDATE sys_msg SET `Title` = ?,Msg = ?,ModifiedTime = ? WHERE Id = ? AND isDeleted = 0";
        $query = $this->db->query($sql,array($data['title'],$data['msg'],date('Y-m-d H:i:s'),$data['id']));
        return $query;
    }

    // 刪除系統通知訊息
    public function delete_sys_msg_by_id($id){
        $sql = "UPDATE sys_msg SET `isDeleted` = ?, DeleteTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d H:i:s'),$id));
        return $query;
    }

    // 系統通知訊息列表
    public function query_sys_msg($data){
        $data['page'] = (int)$data['page'];
        $data['page_count'] = (int)$data['page_count'];
        $PageStar = ($data['page'] - 1) * $data['page_count']; //本頁起始紀錄筆數
        // 取得系統通知訊息資訊
        $all_sql = array(
            "select" => "SELECT * FROM sys_msg WHERE ",
            "where_title" => " Title LIKE ? AND ",
            "where_msg" => " Msg LIKE ? AND ",
            "isDelete" => " isDeleted = 0 ORDER BY CreateTime DESC ",
            "LIMIT" => " LIMIT ?,? ",
        );
        $sql = $all_sql['select'];
        $sql_array = array();
        if ($data['title']) {  // 標題有值
            $sql = $sql . $all_sql['where_title'];
            array_push($sql_array, '%'.$data['title'].'%');
        }
        if ($data['msg']) {  // 訊息有值
            $sql = $sql . $all_sql['where_msg'];
            array_push($sql_array, '%'.$data['msg'].'%');
        }
        $sql = $sql . $all_sql['isDelete'];
        // 取得總數
        $query = $this->db->query($sql, $sql_array);
        $result['total_count'] = $query->num_rows();
        $total_page = $result['total_count'] / $data['page_count'];
        $result['page'] = $data['page'];
        $result['page_count'] = $data['page_count'];
        $result['total_page'] = ceil($total_page);
        // 取得限制筆數資料
        $sql = $sql . $all_sql['LIMIT'];
        array_push($sql_array, $PageStar);
        array_push($sql_array, $data['page_count']);
        $query = $this->db->query($sql, $sql_array);
        $result['sys_msg'] = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Sys_msg_model();
                $obj->id = $row->Id;
                $obj->title = $row->Title;
                $obj->msg = $row->Msg;
                $obj->userId = $row->UserId;
                $obj->createTime = $row->CreateTime;
                $obj->modifiedTime = $row->ModifiedTime;
                array_push($result['sys_msg'], $obj);
            }
        }
        return $result;
    }

    // 取得所有系統通知訊息ID by userId
    public function get_sys_msg_by_userId($userId){
        $sql = "SELECT * FROM sys_msg WHERE isDeleted = 0 AND (UserId = ? OR UserId is NULL) ORDER BY ModifiedTime DESC,CreateTime DESC";
        $query = $this->db->query($sql, array($userId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Sys_msg_model();
                $obj->id = $row->Id;
                $obj->title = $row->Title;
                $obj->msg = $row->Msg;
                $obj->userId = $row->UserId;
                $obj->createTime = $row->CreateTime;
                $obj->modifiedTime = $row->ModifiedTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
