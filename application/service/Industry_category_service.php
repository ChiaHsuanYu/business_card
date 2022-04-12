<?php
class Industry_category_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('industry_category_model');
        $this->load->model('industry_model');
        $this->load->service('common_service');
        $this->load->library('session');
    }
   
    // 取得產業類別
    public function query_all(){
        $r = $this->industry_category_model->get_industry_category();
        if ($r){
            for($i=0;$i<count($r);$i++){
                $r[0]->industry = $this->industry_model->get_industry_by_categoryId($r[$i]->industryCategoryId);
            }
            $result = array(
                "status" => 1,
                "data"=> $r
            );
        }else{
            $result = array(
                "status" => 0,
                "msg"=> "查無資料"
            );    
        }
        return $result;
    }
}