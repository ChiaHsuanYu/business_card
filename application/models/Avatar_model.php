<?php
class Avatar_model extends CI_Model
{
    public $id = '';
    public $imageURL = '';
    public $name = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得系統預設頭像清單
    public function query_all(){
        $sql = "SELECT * FROM avatar";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Avatar_model();
                $obj->id = $row->Id;
                $obj->imageURL = '/'.AVATAR_PATH.$row->ImageURL;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得系統預設頭像資料 by id
    public function get_avatar_by_id($id){
        $sql = "SELECT * FROM avatar WHERE Id = ?";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Avatar_model();
                $obj->id = $row->Id;
                $obj->imageURL = $row->ImageURL;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
