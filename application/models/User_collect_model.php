<?php
class User_collect_model extends CI_Model
{
    public $id = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }
    // 檢查是否已有收藏紀錄
    public function check_user_collect($data){
        $sql = "SELECT Id,isCollected FROM `user_collect` WHERE UserId = ? AND Collect_userId = ? AND isCollected != '0'";
        $query = $this->db->query($sql, array($data['userId'],$data['collect_userId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new User_collect_model();
                $obj->id = $row->Id;
                $obj->isCollected = $row->isCollected;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增收藏紀錄
    public function add_user_collect($data){
        $sql = "INSERT INTO `user_collect` (UserId,Collect_userId,isCollected) VALUES (?,?,?)";
        $query = $this->db->query($sql,array($data['userId'],$data['collect_userId'],$data['isCollected']));
        return $this->db->insert_id();
    }

    // 更新名片收藏狀態
    public function update_isCollected_by_id($data){
        $sql = "UPDATE user_collect SET isCollected = ?, ModifiedTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array($data['isCollected'],date('Y-m-d H:i:s'),$data['collectId']));
        return $query;
    }

    // 取得名片收藏資訊 by id
    public function get_user_collect_by_id($data){
        $sql = "SELECT user_collect.UserId,user_collect.isCollected,users.SuperID,users.Avatar FROM `user_collect` 
                INNER JOIN users ON user_collect.Collect_userId = users.Id WHERE user_collect.Id = ?";
        $query = $this->db->query($sql, array($data['collectId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new User_collect_model();
                $obj->userId = $row->UserId;
                $obj->superID = $row->SuperID;
                $obj->isCollected = $row->isCollected;
                $obj->avatar = '';
                if($row->Avatar){
                    $obj->avatar = $row->Avatar;
                    if(strpos($obj->avatar, "http") === false){
                        $obj->avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                $result = $obj;
            }
        }
        return $result;
    }

    // 取得收藏要求清單 by collect_userId
    public function get_collect_by_collect_userId($data){
        $sql = "SELECT user_collect.UserId,user_collect.isCollected,user_collect.isReaded,users.Name,users.Avatar,users.Nickname,users.SuperID,users.CompanyOrder FROM `user_collect` 
                INNER JOIN users ON user_collect.UserId = users.Id WHERE user_collect.Collect_userId = ? AND user_collect.isCollected = ?";
        $query = $this->db->query($sql, array($data['userId'],$data['isCollected']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new User_collect_model();
                $obj->userId = $row->UserId;
                $obj->name = $row->Name;
                $obj->superID = $row->SuperID;
                $obj->nickname = $row->Nickname;
                $obj->isCollected = $row->isCollected;
                $obj->isReaded = $row->isReaded;
                $obj->avatar = '';
                if($row->Avatar){
                    $obj->avatar = $row->Avatar;
                    if(strpos($obj->avatar, "http") === false){
                        $obj->avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                $obj->companyOrder = explode(',',$row->CompanyOrder);
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新收藏要求已讀狀態
    public function update_collect_isReaded_by_id($collectId){
        $sql = "UPDATE user_collect SET isReaded = 1, ModifiedTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array(date('Y-m-d H:i:s'),$collectId));
        return $query;
    }

    // 查詢收藏的使用者ID
    public function get_collect_users($userId){
        $sql = "SELECT users.* FROM user_collect INNER JOIN `users` ON user_collect.Collect_userId = users.Id 
                WHERE user_collect.UserId = ? AND user_collect.isCollected = 1 AND users.isDeleted = 0 GROUP BY users.Id ORDER BY user_collect.CreateTime DESC";
        $query = $this->db->query($sql, array($userId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new User_collect_model();
                $obj->id = $row->Id;
                $obj->companyOrder = $row->CompanyOrder;
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得被收藏的使用者ID
    public function get_user_for_collected($userId){
        $sql = "SELECT user_collect.Id as collectId,users.* FROM user_collect INNER JOIN `users` ON user_collect.UserId = users.Id 
                WHERE user_collect.Collect_userId = ? AND user_collect.isCollected = 1 AND users.isDeleted = 0 GROUP BY users.Id ORDER BY user_collect.CreateTime DESC";
        $query = $this->db->query($sql, array($userId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new User_collect_model();
                $obj->collectId = $row->collectId;
                $obj->id = $row->Id;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_avatar = '';
                $obj->companyOrder = $row->CompanyOrder;
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Avatar){
                    $obj->personal_avatar = $row->Avatar;
                    if(strpos($obj->personal_avatar, "http") === false){
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
