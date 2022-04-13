<?php
class Industry_model extends CI_Model
{
    public $industryId = '';
    public $industryName = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    // 取得產業名稱
    public function get_industry_by_categoryId($industryCategoryId){
        $sql = "SELECT * FROM industry WHERE IndustryCategoryId = ?";
        $query = $this->db->query($sql, array($industryCategoryId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Industry_model();
                $obj->industryId = $row->Id;
                $obj->industryName = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
