<?php
class Subject_model extends CI_Model
{
    public $id = '';
    public $imageURL = '';
    public $name = '';
    public $isReleased = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得主題清單
    public function query_all($identity = null){
        $sql = "SELECT * FROM `subject` WHERE isDeleted = '0'";
        if($identity === '0'){
            $sql .= " AND isReleased = '1'"; 
        }
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Subject_model();
                $obj->id = $row->Id;
                $obj->imageURL = '/'.SUBJECT_IMAGE_PATH.$row->ImageURL;
                $obj->subjectFile = '/'.SUBJECT_CSS_PATH.$row->SubjectFile;
                $obj->name = $row->Name;
                $obj->isReleased = $row->isReleased;
                $obj->releaseTime = $row->ReleaseTime;
                $obj->createTime = $row->CreateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增主題
    public function add_subject($data){
        $sql = "INSERT INTO `subject` (ImageURL, `SubjectFile`, `Name`) VALUES (?, ?, ?)";
        $query = $this->db->query($sql,array($data['image_path'],$data['css_path'],$data['name']));
        return $this->db->insert_id();
    }

    // 發布主題 by id
    public function update_isReleased_by_id($data){
        $sql = "UPDATE `subject` SET `isReleased` = ?,releaseTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d'),$data['subjectId']));
        return $query;
    }

    // 發布主題 by id
    public function update_isDeleted_by_id($data){
        $sql = "UPDATE `subject` SET `isDeleted` = ?,DeleteTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d'),$data['subjectId']));
        return $query;
    }
}
