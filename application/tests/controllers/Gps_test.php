<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */
// use PHPUnit\Framework\TestCase;
// require_once APPPATH . 'controllers/Users_api.php';
class Gps_test extends TestCase
{
	/**
     * @group Gps
     */
	public function test_gps(){
        // 設置請求參數
        $data = array(
            'lat' => $this->randomFloat(22.6216500, 22.6216600),
            'lng' => $this->randomFloat(120.2964700,120.2964800)
        );
        // 建立模擬對象(setCallable-在控制器實例化後運行)
        $this->request->setCallable(
            function ($CI) {
                // Get mock object
                $common_service = $this->getDouble(
                    'Common_service', [['get_userId_for_session' => ['71737']]]
                );
                $CI->common_service = $common_service;
            }
        );
        // 進行控制器請求
        $result = $this->request(
            'POST',
            'users_api/update_gps',
            $data
        );
        $status = $this->check_status($result);
        print_r(json_decode($result,true));
        $this->assertResponseCode($status);
    }    

    function randomFloat($min = 0, $max = 1) {
        $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return sprintf("%.7f",$num);  //控制小數後幾位
	}
}
