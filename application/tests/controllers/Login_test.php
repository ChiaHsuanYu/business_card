<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */
class Login_test extends TestCase
{
	/**
     * @group Login
     */
	public function test_login(){
		$data = array(
			'account'=>'+886912323062'
		);
        // 進行控制器請求
        $result = $this->request(
            'POST',
            'login_api/login',
            $data
        );
		$status = $this->check_status($result);
        $this->assertResponseCode($status);
	}

	/**
     * @group Login
     */
	// public function test_checkLogin(){
	// 	$response = $this->require('login_api/check_login');
	// 	print_r($response);
	// 	$this->assertStringContainsString('"status":1', $response);
	// }

	
	//  /**
    //  * @depends test_login
    //  */
	// public function test_account_verify()
	// {
	// 	$data = array(
	// 		'vaild'=>'123456'
	// 	);
	// 	$response = $this->require('login_api/account_verify',$data);
	// 	print_r($response);
	// 	$this->assertStringContainsString('"status":1', $response);
	// }

	// public function test_APPPATH()
	// {
	// 	$actual = realpath(APPPATH);
	// 	$expected = realpath(__DIR__ . '/../..');
	// 	$this->assertEquals(
	// 		$expected,
	// 		$actual,
	// 		'Your APPPATH seems to be wrong. Check your $application_folder in tests/Bootstrap.php'
	// 	);
	// }
}
