<?php
class Social_model extends CI_Model
{
    public $id = '';
    public $iconURL = '';
    public $name = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得社群清單
    public function query_all(){
        $sql = "SELECT * FROM social";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Social_model();
                $obj->id = $row->Id;
                $obj->iconURL = '/'.SOCIAL_ICON_PATH.$row->Icon;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得社群資料 by id
    public function get_social_by_id($id){
        $sql = "SELECT * FROM social WHERE Id = ?";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Social_model();
                $obj->id = $row->Id;
                $obj->iconURL = '/'.SOCIAL_ICON_PATH.$row->Icon;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
