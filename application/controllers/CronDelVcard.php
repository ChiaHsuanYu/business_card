<?php
require_once APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class CronDelVcard extends BaseController
{
    public function __construct()
    {
        parent::__construct();  
    }
    public function index()
    {
        //清空資料夾函式和清空資料夾後刪除空資料夾函式的處理
        $path = '/www/wwwroot/shine.sub.sakawa.com.tw/business_card/'.VCARD_PATH;
        if(is_dir($path)){
            //掃描一個資料夾內的所有資料夾和檔案並返回陣列
            $p = scandir($path);
            foreach($p as $val){
                if($val !="." && $val !=".."){
                    //直接刪除檔案
                    unlink($path.$val);
                }
            }
        }
    }
}
