<?php
class Industry_category_model extends CI_Model
{
    public $industryCategoryId = '';
    public $industryCategoryName = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    // 取得產業類別
    public function get_industry_category(){
        $sql = "SELECT * FROM industry_category";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Industry_category_model();
                $obj->industryCategoryId = $row->Id;
                $obj->industryCategoryName = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
