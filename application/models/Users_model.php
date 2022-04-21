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

    // 查詢使用者token是否存在
    public function get_user_by_token($token){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.token = ? AND users.isDeleted = ?";
        $query = $this->db->query($sql, array($token, 0));
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
                // $obj->personal_subjectId = $row->SubjectId;
                // $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                // $obj->subject_name = $row->subjectName;
                // $obj->SMSNumber = $row->SMSNumber;
                // $obj->SMSTime = $row->SMSTime;
                // $obj->isDeleted = $row->isDeleted;
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
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 查詢使用者Id
    public function get_user_by_id($id){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Id = ? AND users.isDeleted = ?";
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
                // $obj->personal_subjectId = $row->SubjectId;
                // $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                // $obj->subject_name = $row->subjectName;
                // $obj->SMSNumber = $row->SMSNumber;
                // $obj->SMSTime = $row->SMSTime;
                // $obj->isDeleted = $row->isDeleted;
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
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token為NULL
    public function update_Token_as_NULL($id){
        $sql = "UPDATE users SET Token = NULL, TokenCreateTime = NULL, TokenUpdateTime = NULL WHERE Id = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($id, 0));
        return $query;
    }

    // 更新使用者token的T_UpdateDT
    public function update_TUpdateDT_by_token($token){
        $sql = "UPDATE users SET TokenUpdateTime = ? WHERE Token = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $token, 0));
        return $query;
    }

    // 更新使用者token
    public function update_Token_by_id($user_id, $token){
        $sql = "UPDATE users SET Token = ?,  TokenCreateTime = ?, TokenUpdateTime = ? WHERE Id = ? AND isDeleted = ?";
        $query = $this->db->query($sql, array($token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user_id, 0));
        return $query;
    }

    public function get_user_by_acc($account){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Account=?";
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
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //登入驗證(管理人員登入用)
    // public function get_user_by_accpwd($account, $password){
    //     $password = md5($password);
    //     $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
    //             LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Account=? AND users.Password=? AND users.Verify=1 AND users.isDeleted = 0";
    //     $query = $this->db->query($sql, array($account, $password));
    //     $result = array();
    //     if ($query->num_rows() > 0) {
    //         foreach ($query->result() as $row) {
    //             $obj = new Users_model();
    //             $obj->id = $row->Id;
    //             $obj->account = $row->Account;
    //             $obj->verify = $row->Verify;
    //             $obj->verifyCode = $row->VerifyCode;
    //             $obj->superID = $row->SuperID;
    //             $obj->name = $row->Name;
    //             $obj->avatar = $row->Avatar;
    //             $obj->subjectId = $row->SubjectId;
    //             $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
    //             $obj->subject_name = $row->subjectName;
    //             $obj->createTime = $row->CreateTime;
    //             $obj->token = $row->Token;
    //             $obj->tokenCreateTime = $row->TokenCreateTime;
    //             $obj->tokenUpdateTime = $row->TokenUpdateTime;
    //             array_push($result, $obj);
    //         }
    //     }
    //     return $result;
    // }

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
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.Id=? AND VerifyCode=? AND users.isDeleted = 0";
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
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
                $obj->subject_name = $row->subjectName;
                $obj->SMSNumber = $row->SMSNumber;
                $obj->SMSTime = $row->SMSTime;
                $obj->isDeleted = $row->isDeleted;
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
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }
    
    // 更改驗證碼 by id
    public function update_verifyCode_by_id($verifyCode,$id){
        $sql = "UPDATE users SET VerifyCode = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($verifyCode,$id));
        return $query;
    }

    // 更改驗證狀態 by id
    public function update_verify_by_id($id){
        $sql = "UPDATE users SET Verify = ? WHERE Id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array('1',$id));
        return $query;
    }

    // 取得使用者資料 by superId
    public function get_user_by_superId($data){
        $sql = "SELECT users.*,`subject`.ImageURL as subject_imageURL,`subject`.Name as subjectName FROM users 
                LEFT JOIN `subject` ON users.subjectId = `subject`.Id WHERE users.SuperID=? AND users.isDeleted = 0";
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
                $obj->personal_subjectId = $row->SubjectId;
                $obj->subject_imageURL = base_url().SUBJECT_IMAGE_PATH.$row->subject_imageURL;
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
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查SUPER ID是否重複
    public function check_superId($data){
        $sql = "SELECT Id FROM users WHERE Id != ? AND SuperID = ? AND isDeleted = 0";
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
        $sql = "UPDATE users SET `isDeleted` = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array($data['isDeleted'],$data['userId']));
        return $query;
    }

    // 使用者名片查詢
    public function query_users($data){
        // $data['page_count'] = (int)$data['page_count']; //字串轉數字
        // $data['page'] = (int)$data['page']; //字串轉數字
        // $PageStar = ($data['page'] - 1) * $data['page_count']; //本頁起始紀錄筆數

        // 取得使用者資訊
        $all_sql = array(
            "select" => "SELECT users.* FROM users LEFT JOIN company ON users.Id = company.UserId WHERE ",
            "where_account" => " users.Account LIKE ? AND ",
            "where_superID" => " users.superID LIKE ? AND ",
            "where_company" => " company.Company LIKE ? AND ",
            "where_industryId" => " company.IndustryId = ? AND ",
            "where_company" => " company.Company LIKE ? AND ",
            "where_dataTime" => " (`users`.CreateTime BETWEEN ? AND ?) AND ",
            "isDelete" => " users.Id != '' GROUP BY users.Id ORDER BY users.CreateTime DESC ",
            // "LIMIT" => " LIMIT ?,? ",
        );
        $sql = $all_sql['select'];
        $sql_array = array();
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
        // $total_page = $result['total_count'] / $data['page_count'];
        // $result['page'] = $data['page'];
        // $result['page_count'] = $data['page_count'];
        // $result['total_page'] = ceil($total_page);
        // // 取得限制筆數資料
        // $sql = $sql . $all_sql['LIMIT'];
        // array_push($sql_array, $PageStar);
        // array_push($sql_array, $data['page_count']);
        // $query = $this->db->query($sql, $sql_array);
        $result['users'] = array();
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
                $obj->isDeleted = $row->isDeleted;
                $obj->createTime = $row->CreateTime;
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
                    $obj->personal_avatar = base_url().AVATAR_PATH.$row->Avatar;
                }
                array_push($result['users'], $obj);
            }
        }
        return $result;
    }
}
