<?php
include APPPATH. 'third_party/gpsdistance/src/Location/Point.php';
include APPPATH. 'third_party/gpsdistance/src/Counter/Calculator.php';
class Gps_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('user_collect_model');
        $this->load->model('contact_setting_model');
        $this->load->model('contact_time_total_model');
        $this->load->model('cancel_contact_total_model');
        $this->load->service('Common_service');
        $this->load->library('session');
        $this->load->driver('cache', array('adapter' => 'redis','backup' => 'file'));
    }
   
    // gps定位-接觸時間累積檢查
    public function check_gps($userId){
        $contact_setting = $this->contact_setting_model->get_contact_setting();
        $max_time = MAX_CONTACT_TIME;
        $min_time = MIN_CONTACT_TIME;
        if($contact_setting){
            $max_time = $contact_setting[0]->max_contact_time;
            $min_time = $contact_setting[0]->min_contact_time;
        }
        // 取得本人定位座標
        if(!$this->cache->redis->get($userId.'_gps')){
            $result = array(
                "status" => 0,
                "msg"=> '查無定位資訊',
            );  
            return $result;
        }
        // 比對與其他人的定位距離並記錄
        $gps_room = $this->check_other_users_gps($userId,$max_time,$min_time);
        $this->cache->redis->save('gps_room',$gps_room,TIME_TO_LIVE);
        $result = array(
            "status" => 1,
            "msg" => '接觸比對完成',
            "gps_room" => $gps_room
        );  
        return $result;
    }

    // 檢查與其他人的定位距離
    public function check_other_users_gps($userId,$max_time,$min_time){
        // 取得個人座標
        $gps = $this->cache->redis->get($userId.'_gps');
        $startingPlace = new Point($gps['lat'], $gps['lng']); 
        $gps_room = array();
        if($this->cache->redis->get('gps_room')){
            $gps_room=$this->cache->redis->get('gps_room');
        }
        // 取得需檢查的人員清單(有開啟AI推薦的使用者/尚未收藏使用者/未在取消接觸累積的使用者列表內/當日接觸時間尚小於max_time的使用者)
        $other_users = $this->users_model->get_other_users_for_gps($userId,$max_time);
        foreach($other_users as $key => $value){
            // 取得他人座標
            $other_id = $value->id;
            if (!$this->cache->redis->get($other_id.'_gps')){
                continue;
            }
            $other_gps = $this->cache->redis->get($other_id.'_gps');
            $destination = new Point($other_gps['lat'], $other_gps['lng']);
            // 計算並判斷距離(單位/公尺)
            $calculator = new Calculator($startingPlace, $destination, $kilometers = true);
            // 檢查是否已有接觸紀錄,二次接觸時才開始計算累積接觸時間
            $room_no = 0;
            $nowtime = strtotime(date('Y-m-d H:i:s'));
            foreach($gps_room as $room_key => $room_val){
                if(in_array($userId,$room_val['users']) && in_array($other_id,$room_val['users'])){
                    $room_no++;
                    $last_check_time = $gps_room[$room_key]['last_check_time'];
                    // 檢查距離
                    $diff_distance = $calculator->getDistance() * 1000;
                    if($diff_distance > DISTANCE){
                        // 設定最新檢查時間
                        $gps_room[$room_key]['last_check_time'] = $nowtime;
                        continue;
                    }
                    if($room_val['check_state'] == 2){
                        break;
                    }
                    $diff_time = $nowtime - $last_check_time;
                    $contact_time = $gps_room[$room_key]['contact_time'] + round(abs($diff_time) / 60,3);
                    $contact_data = array(
                        'userId' => min($userId, $other_id),
                        'other_id' => max($userId, $other_id),
                        'contact_time' => $contact_time,
                        'date' => date('Y-m-d'),
                    );
                    $time_data = array(
                        'max_time' => $max_time,
                        'min_time' => $min_time,
                        'nowtime' => $nowtime
                    );
                    // 設置接觸紀錄(快取&資料庫)
                    $gps_room_data = $this->set_gps_room($contact_data,$time_data,$room_val);
                    // 判斷接觸紀錄狀態,為2時則當日不需再做計算,可移除接觸紀錄快取
                    if($gps_room_data['check_state'] == '2'){
                        array_splice($gps_room, $room_key, 1);
                    }else{
                        $gps_room[$room_key] = $gps_room_data;
                    }
                }
            }
            // 首次接觸,僅記錄尚不累積接觸時間,
            if(!$room_no){
                $room_data = array(
                    'users' => [$userId,$other_id],
                    'check_state' => 0,
                    'contact_time' => 0,
                    'last_check_time' => $nowtime
                );
                array_push($gps_room,$room_data);
            }
        }
        return $gps_room;
    }

    // 設置接觸紀錄(快取&資料庫)
    public function set_gps_room($contact_data,$time_data,$gps_room){
        // 設定最新接觸統計時間
        $gps_room['contact_time'] = round(abs($contact_data['contact_time']),3);
        $gps_room['last_check_time'] = $time_data['nowtime'];
        // 判斷累積接觸時間
        if($contact_data['contact_time'] > $time_data['min_time'] && $contact_data['contact_time'] < $time_data['max_time']){
            // check_state不為1時 寫入資料表
            if($gps_room['check_state'] == '0'){
                $insert_id = $this->contact_time_total_model->add_contact_time_total($contact_data);
                if($insert_id){
                    $gps_room['id'] = $insert_id;
                    $gps_room['check_state'] = 1;
                }else{
                    $this->common_service->logger("add_contact_time_total error:".json_encode($contact_data));
                }
            }
            return $gps_room;
        }
        // 累積接觸時間大於 最大接觸時間時，當日則不需再判斷與該使用者的距離
        if($contact_data['contact_time'] >= $time_data['max_time']){ 
            // check_state為1時 修改資料表
            if($gps_room['check_state']){
                $update_res = $this->contact_time_total_model->update_contact_by_id($gps_room);
                if($update_res){
                    $gps_room['check_state'] = 2;
                }else{
                    $this->common_service->logger("update_contact_by_id error:".json_encode($contact_data));
                }
            }else{
                // 寫入資料表
                $insert_id = $this->contact_time_total_model->add_contact_time_total($contact_data);
                if($insert_id){
                    $gps_room['id'] = $insert_id;
                    $gps_room['check_state'] = 2;
                }else{
                    $this->common_service->logger("add_contact_time_total error:".json_encode($contact_data));
                }
            }
        }
        return $gps_room;
    }

    // 整理取消接觸時間統計名單
    public function cancel_contact_total(){
        $this->common_service->logger("cancel_contact_total start");
        $data = array(
            'startDate' => date("Y-m-d",strtotime("-2 day")),
            'endDate'=> date("Y-m-d",strtotime("-1 day"))
        );
        // 取得已達接觸天數上限資料
        $contact_data = $this->contact_time_total_model->get_contact_cap_data($data);
        foreach($contact_data as $value){
            $data['userId'] = $value->userId;
            $data['contact_userId'] = $value->contact_userId;
            // 檢查取消接觸時間統計名單是否已有資料
            $check_res = $this->cancel_contact_total_model->check_cancel_contact_total($data);
            if($check_res){
                continue;
            }
            // 依序寫入取消接觸時間統計名單
            $cancel_res = $this->cancel_contact_total_model->add_cancel_contact_total($data);
            if(!$cancel_res){
                $this->common_service->logger("add_cancel_contact_total error:".json_encode($data));
            }
        }
        $this->common_service->logger("cancel_contact_total end");
    }
}