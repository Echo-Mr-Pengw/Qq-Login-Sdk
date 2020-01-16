<?php
/**
 * 第三方QQ登录 获取用户唯一标识openid
 * @link https://github.com/Echo-Mr-Pengw
 * @author  new1024kb
 * @since  2020-01-15
 */
namespace Sdk\Api;

require './AccessToken.php';

use Sdk\Api\AccessToken;
use Sdk\Http\Request;
use Sdk\Http\Response;

class OpenId {
	
	/**
	 * 获取openid的地址
	 */
	const OPENID_URL = 'https://graph.qq.com/oauth2.0/me';

	/**
	 * [getUserOpenId 获取QQ用户的唯一标识ID openid]
	 * @param  string $accessToken [令牌]
	 * @return [string]            [access_token]
	 */
	public static function getUserOpenId(string $accessToken) {

		$reqData = [
			'access_token' => $accessToken,
		];

		$ret = Request::get(self::OPENID_URL, $reqData);
		$paserData = self::parseJsonp($ret);

		if(isset($paserData['error'])) {
			Response::error($paserData['error'], $paserData['error_description']);
		}

		if(!isset($paserData['openid'])) {
			Response::error(1007);
		}

		return $paserData['openid'];
	}

	/**
	 * [parseJsonp 解析jsonp]
	 * @param  string $jsonp [jsonp数据]
	 * @return [array]
	 */
	public static function parseJsonp(string $jsonp): array {
		$pattern = '/{.*}/';
		preg_match($pattern, $jsonp, $result);
		return json_decode($result[0], true);
	}
}