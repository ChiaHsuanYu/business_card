<?php
class Subject_model extends CI_Model
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

    // 取得主題清單
    public function query_all(){
        $sql = "SELECT * FROM `subject`";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Subject_model();
                $obj->id = $row->Id;
                $obj->imageURL = base_url().SUBJECT_IMAGE_PATH.$row->ImageURL;
                $obj->name = $row->Name;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增主題
    public function add_subject($data){
        $sql = "INSERT INTO `subject` (ImageURL, `Name`) VALUES (?, ?)";
        $query = $this->db->query($sql,array($data['image_path'],$data['name']));
        return $this->db->insert_id();
    }
}
