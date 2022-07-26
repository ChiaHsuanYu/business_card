<?php
use JeroenDesloovere\VCard\VCard;
class Export_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('company_model');
        $this->load->service('Common_service');
        $this->load->library('session');
    }
    // 匯出Vcf檔
    public function vcard($data){
        $company_data = $this->company_model->get_company_by_id($data['companyId']);
        if(!count($company_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無公司資訊"
            );    
            return $result;
        }
        $user_data = $this->users_model->get_user_by_id($company_data[0]->userId);
        if(!count($user_data)){
            $result = array(
                "status" => 0,
                "msg"=> "查無使用者資訊"
            );   
            return $result;
        }
        $vcard = new VCard();  // define vcard
        // define variables
        $name = $user_data[0]->personal_name;
        $additional = '';
        $prefix = '';
        $suffix = '';
        // add personal data
        $vcard->addName($name, $additional, $prefix, $suffix);
        // add work data
        $vcard->addCompany((!empty($company_data[0]->company_name)?$company_data[0]->company_name:''));
        $vcard->addJobtitle((!empty($company_data[0]->company_position)?$company_data[0]->company_position:''));
        if($company_data[0]->company_aboutus){
            $vcard->addRole((!empty($company_data[0]->company_aboutus)?$company_data[0]->company_aboutus:''));
        }
        $personal_phone = $user_data[0]->personal_phone;
        $personal_avatar = $user_data[0]->personal_avatar;
        $personal_email = $user_data[0]->personal_email;
        $personal_social = $user_data[0]->personal_social;
        $company_phone = $company_data[0]->company_phone;
        $company_social = $company_data[0]->company_social;
        $company_email = $company_data[0]->company_email;
        $avatar_path = explode(base_url(), $personal_avatar);
        
        if($personal_phone){
            for($i=0;$i<count($personal_phone);$i++){
                $vcard->addPhoneNumber((!empty($personal_phone[$i])?$personal_phone[$i]:''), 'WORK');
            }
        }
        if($company_phone){
            for($i=0;$i<count($company_phone);$i++){
                $vcard->addPhoneNumber((!empty($company_phone[$i])?$company_phone[$i]:''),'WORK');
            }
        }
        if($personal_email){
            for($i=0;$i<count($personal_email);$i++){
                $vcard->addEmail((!empty($personal_email[$i])?$personal_email[$i]:''));
            }
        }
        if($company_email){
            for($i=0;$i<count($company_email);$i++){
                $vcard->addEmail((!empty($company_email[$i])?$company_email[$i]:''));
            }
        }
        if($personal_social){
            for($i=0;$i<count($personal_social);$i++){
                $vcard->addURL((!empty($personal_social[$i]->socialURL)?$personal_social[$i]->socialURL:''));
            }
        }
        if($company_social){
            for($i=0;$i<count($company_social);$i++){
                $vcard->addURL((!empty($company_social[$i]->socialURL)?$company_social[$i]->socialURL:''));
            }
        }
        if(count($avatar_path)>1){
            if(file_exists($avatar_path[1])){
                $vcard->addPhoto($avatar_path[1]);
            }
        }
        $company_logo = $company_data[0]->company_logo;
        $logo_path = explode(base_url(), $company_logo);
        if(count($logo_path)>1){
            if(file_exists($logo_path[1])){
                $vcard->addLogo($logo_path[1]);
            }
        }

        $superId = $user_data[0]->personal_superID;
        $superId = str_replace("_","-",$superId);
        $superId = strtolower($superId);
        $file_name = $superId.'-business-card';
        $no = 0;
        while(file_exists(VCARD_PATH.$file_name.'.vcf')){
            $no++;
            $file_name = $superId.'-business-card-'.$no;
        }
        $vcard->setFilename($file_name);
        // return vcard as a string
        // $vcard->getOutput();
        $vcard->setSavePath(VCARD_PATH);
        $vcard->save();        
        $result = array(
            "status" => 1,
            "data"=> base_url().VCARD_PATH.$file_name.'.vcf'
        );    
        return $result;
    }
}