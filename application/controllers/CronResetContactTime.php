<?php
require APPPATH . 'controllers/BaseController.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class CronResetContactTime extends BaseController
{
    public function __construct()
    {
        parent::__construct();        
        $this->load->helper('url');
        $this->load->service('Gps_service');
        $this->load->driver('cache', array('adapter' => 'redis','backup' => 'file'));
    }
    // 整理取消接觸時間統計名單
    public function index(){
        if(!$this->input->is_cli_request()) {//檢測對控制器的請求是否來自命令行
            echo "執行方式錯誤";
        }else{
            $this->gps_service->cancel_contact_total();
        }
    }
}
