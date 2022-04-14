<?php
class Company_model extends CI_Model
{
    public $id = '';
    public $order = '';
    public $company_name = '';
    public $company_address = '';
    public $company_gui = '';
    public $company_phone = '';
    public $company_industryCategoryId = '';
    public $company_industryCategoryName = '';
    public $company_industryId = '';
    public $company_industryName = '';
    public $company_position = '';
    public $company_aboutus = '';
    public $company_email = '';
    public $company_logo = '';
    public $company_social = '';
    public $createTime = '';
   
    // 連接資料庫
    public function __construct(){
        $this->load->database();
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
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
