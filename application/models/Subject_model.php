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

    // 取得主題資料 by id
    public function get_subject($data){
        $sql = "SELECT `subject`.*,template.Template FROM `subject` LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE `subject`.Id = ? AND `subject`.isDeleted = '0'";
        $query = $this->db->query($sql, array($data['id']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Subject_model();
                $obj->id = $row->Id;
                $obj->templateId = $row->TemplateId;
                $obj->template = $row->Template;
                $obj->imageURL = base_url().SUBJECT_IMAGE_PATH.$row->ImageURL;
                $obj->subjectFile = base_url().SUBJECT_CSS_PATH.$row->SubjectFile;
                $obj->subjectFileName = $row->SubjectFile;
                $obj->name = $row->Name;
                $obj->isReleased = $row->isReleased;
                $obj->releaseTime = $row->ReleaseTime;
                $obj->createTime = $row->CreateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得主題清單
    public function query_all($identity = null){
        $sql = "SELECT `subject`.*,template.Template FROM `subject` LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE `subject`.isDeleted = '0'";
        if($identity === '0'){
            $sql .= " AND isReleased = '1'"; 
        }
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Subject_model();
                $obj->id = $row->Id;
                $obj->template = $row->Template;
                $obj->imageURL = base_url().SUBJECT_IMAGE_PATH.$row->ImageURL;
                $obj->subjectFile = base_url().SUBJECT_CSS_PATH.$row->SubjectFile;
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
        $sql = "INSERT INTO `subject` (TemplateId, ImageURL, `SubjectFile`, `Name`) VALUES (?, ?, ?, ?)";
        $query = $this->db->query($sql,array($data['templateId'],$data['image_path'],$data['css_path'],$data['name']));
        return $this->db->insert_id();
    }

    // 修改主題 by id
    public function update_subject_by_id($data){
        $sql = "UPDATE `subject` SET `TemplateId` = ?,`ImageURL` = ?,SubjectFile = ?,`Name` = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array($data['templateId'],$data['image_path'],$data['css_path'],$data['name'],$data['id']));
        return $query;
    }

    // 發布主題 by id
    public function update_isReleased_by_id($data){
        $sql = "UPDATE `subject` SET `isReleased` = ?,releaseTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d'),$data['subjectId']));
        return $query;
    }

    // 刪除主題 by id
    public function update_isDeleted_by_id($data){
        $sql = "UPDATE `subject` SET `isDeleted` = ?,DeleteTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d H:i:s'),$data['subjectId']));
        return $query;
    }
}
