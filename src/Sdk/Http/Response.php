<?php 
/**
 * 返回
 * @link https://github.com/Echo-Mr-Pengw
 * @author  new1024kb
 * @since  2020-01-15
 */
namespace Sdk\Http;

class Response {

	public static function error($errno, $errmsg = '') {

		if($errmsg) {
			$msg = compact('errno', 'errmsg');
			echo json_encode($msg);
			exit;
		}
		$msg = [
			0    => ['errno' => 0, 'errmsg' => 'success'],
			1001 => ['errno' => 1001, 'errmsg' => '缺少appId参数'],
			1002 => ['errno' => 1002, 'errmsg' => '缺少appKey参数'],
			1003 => ['errno' => 1003, 'errmsg' => '缺少authCode参数'],
			1004 => ['errno' => 1004, 'errmsg' => '缺少redirectUrl参数'],
			1005 => ['errno' => 1005, 'errmsg' => 'auth code 为空'],
			1006 => ['errno' => 1006, 'errmsg' => 'access token为空'],
			1007 => ['errno' => 1007, 'errmsg' => 'openid为空'],
		];

		echo json_encode($msg[$errno]);
		exit;
	}
}