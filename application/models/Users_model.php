<?php
class Users_model extends CI_Model
{
    public $id = '';
    public $account = '';
    public $order = '';
    public $companyOrder = '';
    public $personal_superID = '';
    public $personal_name = '';
    public $personal_avatar = '';
    public $personal_nickname = '';
    public $personal_email = '';
    public $personal_phone = '';
    public $personal_social = '';
    public $personal_subjectId = '';

    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 查詢使用者Id
    public function get_user_by_id($id){
        $sql = "SELECT users.* FROM users WHERE users.Id = ? AND users.isDeleted = ?";
        $query = $this->db->query($sql, array($id, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                // strpos($mystring, "program.")
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    public function get_user_by_acc($account){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName,`template`.Template FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE users.Account=?";
        $query = $this->db->query($sql, array($account));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                $obj->subject_template = $row->Template;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = base_url().SUBJECT_CSS_PATH.$row->subject_file;
                $obj->Template = $row->Template;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查手機號碼是否存在
    public function check_account($account){
        $sql = "SELECT Id,Account,Verify FROM users WHERE Account=? AND isDeleted = 0";
        $query = $this->db->query($sql, array($account));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->verify = $row->Verify;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增使用者
    public function add_user($account){
        $sql = "INSERT INTO users (Account, CreateTime) VALUES (?, ?)";
        $query = $this->db->query($sql,array($account,date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 帳號驗證
    public function check_verify_by_id($data){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName,`template`.Template FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE users.Id=? AND VerifyCode=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($data['userId'],$data['vaild']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                $obj->subject_template = $row->Template;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = base_url().SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }
    
    // 更改驗證碼 by id
    public function update_verifyCode_by_id($verifyCode,$SMSNumber,$id){
        $sql = "UPDATE users SET VerifyCode = ?,SMSNumber = ?,SMSTime = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($verifyCode,$SMSNumber,date('Y-m-d H:i:s'),$id));
        return $query;
    }

    // 更改驗證狀態 by id
    public function update_verify_by_id($id){
        $sql = "UPDATE users SET Verify = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array('1',$id));
        return $query;
    }

    // 檢查google帳戶是否存在
    public function check_user_by_google_uid($google_uid){
        $sql = "SELECT Id,isDeleted FROM users WHERE Google_uid=?";
        $query = $this->db->query($sql, array($google_uid));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->isDeleted = $row->isDeleted;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // google登入驗證
    public function get_user_by_google_access_token($access_token){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName,`template`.Template FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE users.Google_access_token=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($access_token));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                $obj->subject_template = $row->Template;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = base_url().SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更改google access Token by google_uid
    public function update_google_access_token($access_token,$google_uid){
        $sql = "UPDATE users SET Google_access_token = ? WHERE Google_uid = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($access_token,$google_uid));
        return $query;
    }
    
    // 新增google使用者
    public function add_google_user($data){
        $sql = "INSERT INTO users (Google_uid, Google_access_token, Name, Avatar, Email, CreateTime) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql,array($data['google_uid'],$data['google_access_token'],$data['name'],$data['avatar'],$data['email'],date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 檢查facebook帳戶是否存在
    public function check_user_by_facebook_uid($facebook_uid){
        $sql = "SELECT Id,isDeleted FROM users WHERE Facebook_uid=? AND isDeleted = 0";
        $query = $this->db->query($sql, array($facebook_uid));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->isDeleted = $row->isDeleted;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // facebook登入驗證
    public function get_user_by_facebook_access_token($access_token){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName,`template`.Template FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE users.Facebook_access_token=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($access_token));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                $obj->subject_template = $row->Template;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = base_url().SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更改facebook access Token by facebook_uid
    public function update_facebook_access_token($access_token,$facebook_uid){
        $sql = "UPDATE users SET Facebook_access_token = ? WHERE Facebook_uid = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($access_token,$facebook_uid));
        return $query;
    }
    
    // 新增facebook使用者
    public function add_facebook_user($data){
        $sql = "INSERT INTO users (Facebook_uid, Facebook_access_token, Name, Avatar, Email, CreateTime) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql,array($data['facebook_uid'],$data['facebook_access_token'],$data['name'],$data['avatar'],$data['email'],date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    
    // 檢查line帳戶是否存在
    public function check_user_by_line_uid($line_uid){
        $sql = "SELECT Id,isDeleted FROM users WHERE Line_uid=? AND isDeleted = 0";
        $query = $this->db->query($sql, array($line_uid));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->isDeleted = $row->isDeleted;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // line登入驗證
    public function get_user_by_line_access_token($access_token){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName,`template`.Template FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE users.Line_access_token=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($access_token));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                $obj->subject_template = $row->Template;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = base_url().SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更改line access Token by line_uid
    public function update_line_access_token($access_token,$line_uid){
        $sql = "UPDATE users SET Line_access_token = ? WHERE Line_uid = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($access_token,$line_uid));
        return $query;
    }
    
    // 新增line使用者
    public function add_line_user($data){
        $sql = "INSERT INTO users (Line_uid, Line_access_token, Name, Avatar, CreateTime) VALUES (?, ?, ?, ?, ?)";
        $query = $this->db->query($sql,array($data['line_uid'],$data['line_access_token'],$data['name'],$data['avatar'],date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 取得使用者資料 by superId
    public function get_user_by_superId($data){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.SubjectFile as subject_file,`subject`.Name as subjectName,`template`.Template FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id LEFT JOIN `template` ON template.Id = `subject`.TemplateId WHERE users.SuperID=? AND users.isDeleted = 0";
        $query = $this->db->query($sql, array($data['superId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
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
                $obj->subject_template = $row->Template;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_file = base_url().SUBJECT_CSS_PATH.$row->subject_file;
                $obj->subject_name = $row->subjectName;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查SUPER ID是否重複
    public function check_superId($data){
        $sql = "SELECT Id FROM users WHERE Id != ? AND SuperID = ?";
        $query = $this->db->query($sql, array($data['id'],$data['superId']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 修改基本資料 by id
    public function update_personal_by_id($data){
        $sql = "UPDATE users SET `SuperID` = ?,`Name` = ?,`Nickname` = ?,`Avatar` = ?,`CompanyOrder` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($data['personal_superID'],$data['personal_name'],$data['personal_nickname'],$data['personal_avatar_path'],$data['companyOrder'],$data['id']));
        return $query;
    }

    // 更改使用者主題 by id
    public function update_subjectId_by_id($data){
        $sql = "UPDATE users SET `SubjectId` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($data['subjectId'],$data['id']));
        return $query;
    }

    // 修改SUPER ID by userId
    public function update_superId_by_id($data){
        $sql = "UPDATE users SET `SuperID` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($data['superId'],$data['id']));
        return $query;
    }

    // 修改個人檔案 by id
    public function update_acc_by_id($data){
        $sql = "UPDATE users SET `Name` = ?,`Nickname` = ?,`Avatar` = ?,`Phone` = ?,`Email` = ?,`Social` = ?,`Order` = ?,`CompanyOrder` = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($data->personal_name,$data->personal_nickname,$data->personal_avatar_path,$data->personal_phone,$data->personal_email,$data->personal_social,$data->order,$data->company_order,$data->id));
        return $query;
    }

    // 修改帳號狀態(凍結/解凍) by id
    public function update_isDeleted_by_id($data){
        $sql = "UPDATE users SET `isDeleted` = ?, DeleteTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array($data['isDeleted'],date('Y-m-d H:i:s'),$data['userId']));
        return $query;
    }

    // 使用者名片查詢
    public function query_users($data){
        $data['page_count'] = (int)$data['page_count']; //字串轉數字
        $data['page'] = (int)$data['page']; //字串轉數字
        $PageStar = ($data['page'] - 1) * $data['page_count']; //本頁起始紀錄筆數

        // 取得使用者資訊
        $all_sql = array(
            "select" => "SELECT users.* FROM users ",
            "left_join_company" => " LEFT JOIN company ON users.Id = company.UserId ",
            "where" => " WHERE ",
            "where_account" => " users.Account LIKE ? AND ",
            "where_superID" => " users.superID LIKE ? AND ",
            "where_company" => " company.Company LIKE ? AND ",
            "where_industryId" => " company.IndustryId = ? AND ",
            "where_dataTime" => " (`users`.CreateTime BETWEEN ? AND ?) AND ",
            "isDelete" => " users.Id != '' GROUP BY users.Id ORDER BY users.CreateTime DESC ",
            "LIMIT" => " LIMIT ?,? ",
        );
        $sql = $all_sql['select'];
        $sql_array = array();
        if(!empty($data['company']) || !empty($data['industryId']) || !empty($data['company'])){
            $sql = $sql . $all_sql['left_join_company'];
        }
        $sql = $sql. $all_sql['where'];
        if ($data['account']) {  // 帳號有值
            $sql = $sql . $all_sql['where_account'];
            array_push($sql_array, '%'.$data['account'].'%');
        }
        if ($data['superID']) {  // SUPER ID有值
            $sql = $sql . $all_sql['where_superID'];
            array_push($sql_array, '%'.$data['superID'].'%');
        }
        if ($data['company']) {  // 公司名稱有值
            $sql = $sql . $all_sql['where_company'];
            array_push($sql_array, '%'.$data['company'].'%');
        }
        if ($data['industryId']) {  // 產業ID有值
            $sql = $sql . $all_sql['where_industryId'];
            array_push($sql_array, $data['industryId']);
        }
        if (!empty($data['startDT']) && !empty($data['endDT'])) {  // 期間有值
            $sql = $sql . $all_sql['where_dataTime'];
            array_push($sql_array, $data['startDT'], $data['endDT']);
        }
        // 加上JOIN及isDelete篩選條件
        $sql = $sql . $all_sql['isDelete'];

        // 取得總數
        $query = $this->db->query($sql, $sql_array);
        $result['total_count'] = $query->num_rows();
        $total_page = $result['total_count'] / $data['page_count'];
        $result['page'] = $data['page'];
        $result['page_count'] = $data['page_count'];
        $result['total_page'] = ceil($total_page);
        // 取得限制筆數資料
        $sql = $sql . $all_sql['LIMIT'];
        array_push($sql_array, $PageStar);
        array_push($sql_array, $data['page_count']);
        $query = $this->db->query($sql, $sql_array);
        $result['users'] = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->Id;
                $obj->account = $row->Account;
                $obj->google_uid = $row->Google_uid;
                $obj->facebook_uid = $row->Facebook_uid;
                $obj->line_uid = $row->Line_uid;
                $obj->order = $row->Order;
                $obj->companyOrder = $row->CompanyOrder;
                $obj->personal_superID = $row->SuperID;
                $obj->personal_name = $row->Name;
                $obj->personal_avatar = $row->Avatar;
                $obj->personal_nickname = $row->Nickname;
                $obj->personal_email = $row->Email;
                $obj->personal_phone = $row->Phone;
                $obj->personal_social = json_decode($row->Social);
                $obj->isDeleted = $row->isDeleted;
                $obj->createTime = $row->CreateTime;
                $obj->modifiedTime = $row->ModifiedTime;
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
                        $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                    };
                }
                array_push($result['users'], $obj);
            }
        }
        return $result;
    }
}
