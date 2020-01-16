<?php
/**
 * 第三方QQ登录 通过code获取令牌
 * @link https://github.com/Echo-Mr-Pengw
 * @author  new1024kb
 * @since  2020-01-15
 */
namespace Sdk\Api;

require './../Http/Request.php';
require './../Http/Response.php';

use Sdk\Http\Request;
use Sdk\Http\Response;

class AccessToken {
	
	/**
	 * 获取access_token的地址
	 */
	const ACCESS_TOKE_URL = 'https://graph.qq.com/oauth2.0/token';

	/**
	 * [getAccessToken 获取access_token的方法]
	 * @param  string $appKey      [appkey]
	 * @param  string $appSecret   [appSecret]
	 * @param  string $authCode    [Authorization Code]
	 * @param  string $redirectUrl [回调地址]
	 * @return [string]            [access_token]
	 */
	public static function getAccessToken(string $appKey, string $appSecret, string $authCode, string $redirectUrl) {

		$reqData = [
			'grant_type'    => 'authorization_code',
			'client_id'     => $appKey,
			'client_secret' => $appSecret,
			'code'          => $authCode,
			'redirect_uri'  => $redirectUrl,
		];

		$ret = Request::get(self::ACCESS_TOKE_URL, $reqData);
		if(!is_array($ret)) {
			Response::error(1001, $ret);
		}
		parse_str($ret, $parseData);
		if(empty($parseData['access_token'])) {
			Response::error(1006);
		}
		return $parseData['access_token'];
	}
}