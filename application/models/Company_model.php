<?php
class Company_model extends CI_Model
{
    public $id = '';
    public $order = '';
    public $company_name = '';
    public $company_address = '';
    public $company_gui = '';
    public $company_phone = '';
    public $company_industryId = '';
    // public $company_industryName = '';
    public $company_position = '';
    public $company_aboutus = '';
    public $company_email = '';
    public $company_logo = '';
    public $company_social = '';
    public $createTime = '';
   
    // 連接資料庫
    public function __construct(){
        $this->load->database();
        $this->load->helper('url');
    }

    // 取得公司資訊 by userId
    public function get_company_by_userId($companyId,$userId){
        $sql = "SELECT company.*,industry.Name as industryName FROM company 
                LEFT JOIN industry ON company.IndustryId = industry.Id 
                WHERE company.Id = ? AND company.UserId = ? AND company.isDeleted = ?";
        $query = $this->db->query($sql, array($companyId,$userId, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Company_model();
                $obj->id = $row->Id;
                $obj->userId = $row->UserId;
                $obj->order = $row->Order;
                $obj->company_name = $row->Company;
                $obj->company_address = $row->Address;
                $obj->company_gui = $row->Gui;
                $obj->company_phone = $row->Phone;
                $obj->company_industryId = $row->IndustryId;
                $obj->company_industryName = $row->industryName;
                $obj->company_position = $row->Position;
                $obj->company_aboutus = $row->Aboutus;
                $obj->company_email = $row->Email;
                $obj->company_logo = $row->Logo;
                $obj->company_social = json_decode($row->Social);
                $obj->createTime = $row->CreateTime;
                $obj->modifiedTime = $row->ModifiedTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->Phone){
                    $obj->company_phone = explode(',',$row->Phone);
                }
                if($row->Email){
                    $obj->company_email = explode(',',$row->Email);
                }
                if($row->Address){
                    $obj->company_address = explode(',',$row->Address);
                }
                if($row->Logo){
                    $obj->company_logo = base_url().LOGO_PATH.$row->Logo;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得公司資訊 by companyId
    public function get_company_by_id($companyId){
        $sql = "SELECT company.*,industry.Name as industryName FROM company 
                LEFT JOIN industry ON company.IndustryId = industry.Id 
                WHERE company.Id = ? AND company.isDeleted = ?";
        $query = $this->db->query($sql, array($companyId, 0));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Company_model();
                $obj->id = $row->Id;
                $obj->userId = $row->UserId;
                $obj->order = $row->Order;
                $obj->company_name = $row->Company;
                $obj->company_address = $row->Address;
                $obj->company_gui = $row->Gui;
                $obj->company_phone = $row->Phone;
                $obj->company_industryId = $row->IndustryId;
                $obj->company_industryName = $row->industryName;
                $obj->company_position = $row->Position;
                $obj->company_aboutus = $row->Aboutus;
                $obj->company_email = $row->Email;
                $obj->company_logo = $row->Logo;
                $obj->company_social = json_decode($row->Social);
                $obj->createTime = $row->CreateTime;
                $obj->modifiedTime = $row->ModifiedTime;
                if($row->Order){
                    $obj->order = explode(',',$row->Order);
                }
                if($row->Phone){
                    $obj->company_phone = explode(',',$row->Phone);
                }
                if($row->Email){
                    $obj->company_email = explode(',',$row->Email);
                }
                if($row->Address){
                    $obj->company_address = explode(',',$row->Address);
                }
                if($row->Logo){
                    $obj->company_logo = base_url().LOGO_PATH.$row->Logo;
                }
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增公司資訊
    public function add_company($data){
        $sql = "INSERT INTO company (UserId,Company,Position,Logo,`Order`,CreateTime) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql,array($data['id'],$data['company_name'],$data['company_position'],$data['company_logo_path'],COMPANY_ORDER,date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 新增公司資訊 for 編輯個人檔案
    public function add_company_for_acc($userId,$data){
        $sql = "INSERT INTO company (UserId,`Order`,Company,`Address`,Gui,Phone,IndustryId,Position,Aboutus,Email,Logo,Social,CreateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql,array($userId,$data->order,$data->company_name,$data->company_address,$data->company_gui,$data->company_phone,$data->company_industryId,$data->company_position,$data->company_aboutus,$data->company_email,$data->company_logo_path,$data->company_social,date('Y-m-d H:i:s')));
        return $this->db->insert_id();
    }

    // 刪除公司資訊 by userId
    // public function del_company_by_userId($userId){
    //     $sql = "DELETE FROM company WHERE company.UserId = ? ";
    //     $query = $this->db->query($sql, array($userId));
    //     return $query;
    // }

    // 修改公司資訊 for 編輯個人檔案
    public function update_company_for_id($data){
        $sql = "UPDATE company SET `Order` = ?,Company = ?,`Address` = ?,Gui = ?,Phone = ?,IndustryId = ?,Position = ?,Aboutus = ?,Email = ?,Logo = ?,Social = ?,ModifiedTime = ? WHERE Id = ?";
        $query = $this->db->query($sql,array($data->order,$data->company_name,$data->company_address,$data->company_gui,$data->company_phone,$data->company_industryId,$data->company_position,$data->company_aboutus,$data->company_email,$data->company_logo_path,$data->company_social,date('Y-m-d H:i:s'),$data->id));
        return $query;
    }

    // 刪除公司資訊 by id
    public function del_company_by_id($id){
        $sql = "UPDATE company SET `isDeleted` = ?, DeleteTime = ? WHERE Id = ?;";
        $query = $this->db->query($sql, array('1',date('Y-m-d H:i:s'),$id));
        return $query;
    }

    // 取得符合條件的公司資訊
    public function check_company_for_random($id,$data){
        $all_sql = array(
            "select" => "SELECT Id FROM company WHERE Id = ? AND isDeleted = 0",
            "where_industryId" => " AND IndustryId = ? ",
            "where_address" => " AND `Address` LIKE ? ",
        );
        $sql = $all_sql['select'];
        $sql_array = array($id);
        if(!empty($data['industryId'])){
            $sql .= $all_sql['where_industryId'];
            array_push($sql_array,$data['industryId']);
        }
        if(!empty($data['area_name'])){
            $sql .= $all_sql['where_address'];
            array_push($sql_array,'%'.$data['area_name'].'%');
        }
        $query = $this->db->query($sql, $sql_array);
        return $query->num_rows();
    }
}
