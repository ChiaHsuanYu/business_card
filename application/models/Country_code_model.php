<?php
class Country_code_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得各國國碼清單
    public function query_all(){
        $sql = "SELECT * FROM country_code WHERE isDeleted = '0'";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Country_code_model();
                $obj->id = $row->Id;
                $obj->country = $row->Country;
                $obj->code = $row->Code;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
