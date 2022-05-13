<?php
class Token_model extends CI_Model
{
    public $id = '';
    public $host = '';
    public $token = '';
    public $tokenCreateTime = '';
    public $tokenUpdateTime = '';

    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 檢查使用者裝置是否已存在
    public function check_host_by_userId($user_id,$host,$device){
        $sql = "SELECT token.* FROM token WHERE token.UserId = ? AND token.Host = ? AND token.Device = ?";
        $query = $this->db->query($sql, array($user_id,$host,$device));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Token_model();
                $obj->id = $row->Id;
                $obj->userId = $row->UserId;
                $obj->host = $row->Host;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token
    public function update_Token_by_id($id, $token){
        $sql = "UPDATE token SET Token = ?,  TokenCreateTime = ?, TokenUpdateTime = ? WHERE Id = ?";
        $query = $this->db->query($sql, array($token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id));
        return $query;
    }

    // 新增使用者裝置token
    public function add_token($user_id,$token,$host,$device){
        $sql = "INSERT INTO token (UserId, Host, Device, Token, TokenCreateTime, TokenUpdateTime) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql,array($user_id, $host, $device, $token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 查詢使用者token是否存在
    public function get_user_by_token($token){
        $sql = "SELECT users.*,token.UserId,token.Host,token.Token,token.TokenCreateTime,token.TokenUpdateTime,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName 
                FROM users LEFT JOIN `token` ON token.UserId = `users`.Id LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE token.token = ? AND users.isDeleted = ?";
        $query = $this->db->query($sql, array($token, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Token_model();
                $obj->id = $row->Id;
                $obj->google_uid = $row->Google_uid;
                $obj->google_access_token = $row->Google_access_token;
                $obj->facebook_uid = $row->Facebook_uid;
                $obj->facebook_access_token = $row->Facebook_access_token;
                $obj->line_uid = $row->Line_uid;
                $obj->line_access_token = $row->Line_access_token;
                $obj->account = $row->Account;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = '/'.SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = '/'.SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
                $obj->isDeleted = $row->isDeleted;
                $obj->identity = $row->Identity;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Email){
                    $obj->personal_email = explode(',',$row->Email);
                }
                if($row->Phone){
                    $obj->personal_phone = explode(',',$row->Phone);
                }
                if($row->Avatar){
                    $obj->personal_avatar = $row->Avatar;
                    if(strpos($obj->personal_avatar, "http") === false){
                        $obj->personal_avatar = '/'.AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 查詢使用者Id
    public function get_user_by_id($id){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Id = ? AND users.isDeleted = ?";
        $query = $this->db->query($sql, array($id, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Token_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = '/'.SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = '/'.SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
                // $obj->SMSNumber = $row->SMSNumber;
                // $obj->SMSTime = $row->SMSTime;
                // $obj->isDeleted = $row->isDeleted;
                $obj->identity = $row->Identity;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->CompanyOrder){
                    $obj->companyOrder = explode(',',$row->CompanyOrder);
                }
                if($row->Email){
                    $obj->personal_email = explode(',',$row->Email);
                }
                if($row->Phone){
                    $obj->personal_phone = explode(',',$row->Phone);
                }
                if($row->Avatar){
                    $obj->personal_avatar = $row->Avatar;
                    if(strpos($obj->personal_avatar, "http") === false){
                        $obj->personal_avatar = '/'.AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token為NULL by token
    public function update_Token_as_NULL($token){
        $sql = "UPDATE token SET Token = NULL WHERE Token = ?";
        $query = $this->db->query($sql, array($token));
        return $query;
    }

    // 更新使用者token的T_UpdateDT
    public function update_TUpdateDT_by_token($token){
        $sql = "UPDATE token SET TokenUpdateTime = ? WHERE Token = ?";
        $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $token));
        return $query;
    }

    // 取得已登入設備裝置數量
    public function get_login_device($user_id,$device){
        $sql = "SELECT token.* FROM token WHERE token.UserId = ? AND token.Device = ? AND token.Token != ?";
        $query = $this->db->query($sql, array($user_id,$device,''));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Token_model();
                $obj->id = $row->Id;
                $obj->userId = $row->UserId;
                $obj->host = $row->Host;
                $obj->token = $row->Token;
                $obj->tokenCreateTime = $row->TokenCreateTime;
                $obj->tokenUpdateTime = $row->TokenUpdateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
