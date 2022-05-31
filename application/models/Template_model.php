<?php
class Template_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得模板清單
    public function query_all(){
        $sql = "SELECT * FROM `template` WHERE isDeleted = '0'";
        $query = $this->db->query($sql, array());
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Template_model();
                $obj->id = $row->Id;
                $obj->template = $row->Template;
                $obj->createTime = $row->CreateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得模板資料 by id
    public function get_template($data){
        $sql = "SELECT `template`.* FROM `template` WHERE `template`.Id = ? AND `template`.isDeleted = '0'";
        $query = $this->db->query($sql, array($data['id']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Template_model();
                $obj->id = $row->Id;
                $obj->template = $row->Template;
                $obj->createTime = $row->CreateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增模板
    public function add_template($data){
        $sql = "INSERT INTO `template` (Template) VALUES (?)";
        $query = $this->db->query($sql,array($data['template']));
        return $this->db->insert_id();
    }

    // 修改模板 by id
    public function update_template_by_id($data){
        $sql = "UPDATE `template` SET `Template` = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array($data['template'],$data['id']));
        return $query;
    }

    // 刪除模板 by id
    public function update_isDeleted_by_id($data){
        $sql = "UPDATE `template` SET `isDeleted` = ?,DeleteTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d H:i:s'),$data['id']));
        return $query;
    }
}
