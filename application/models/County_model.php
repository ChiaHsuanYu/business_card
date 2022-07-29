<?php
class County_model extends CI_Model
{
    public $id = '';
    public $name = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    // 取得行政區
    public function query_all(){
        $sql = "SELECT * FROM county";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new County_model();
                $obj->id = $row->Id;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
    // 取得行政區 by id
    public function get_county_by_id($id){
        $sql = "SELECT * FROM county WHERE Id = ?";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new County_model();
                $obj->id = $row->Id;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
