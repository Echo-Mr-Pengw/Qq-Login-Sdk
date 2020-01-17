<?php
/**
 * 第三方QQ登录SDK
 * @link https://github.com/Echo-Mr-Pengw
 * @author  new1024kb
 * @since  2020-01-15
 */
class QqLoginSdk {

	/**
	 * [$appId 申请QQ登录成功后，分配给网站的appid]
	 * @var [string]
	 */
	public $appId;

	/**
	 * [$appKey 申请QQ登录成功后，分配给网站的appkey]
	 * @var [string]
	 */
	public $appKey;

	/**
	 * [$authCode auth code]
	 * @var [string]
	 */
	public $authCode;
	/**
	 * [$redirectUrl 成功授权后的回调地址，必须是注册appid时填写的主域名下的地址，建议设置为网站首页或网站的用户中心。注意需要将url进行URLEncode]
	 * @var [string]
	 */
	public $redirectUrl;

	/**
	 * 获取access_token的地址
	 */
	const ACCESS_TOKE_URL = 'https://graph.qq.com/oauth2.0/token';

	/**
	 * 获取openid的地址
	 */
	const OPENID_URL = 'https://graph.qq.com/oauth2.0/me';

	/**
	 * 获取登录用户在QQ空间的信息，包括昵称、头像、性别及黄钻信息的地址
	 */
	const USER_INFO_URL = 'https://graph.qq.com/user/get_user_info';

	/**
	 * [__construct 构造函数]
	 * @param string $appId       [appId]
	 * @param string $appKey      [appKey]
	 * @param string $authCode    [Authorization Code]
	 * @param [type] $redirectUrl [成功授权后的回调地址]
	 */
	public function __construct(string $appId, string $appKey, string $authCode, string $redirectUrl) {

		if(empty($appId)) {
			self::error(1001);
		}

		if(empty($appKey)) {
			self::error(1002);
		}

		if(empty($authCode)) {
			self::error(1003);
		}

		if(empty($redirectUrl)) {
			self::error(1004);
		}

		$this->appId    = $appId;
		$this->appKey   = $appKey;
		$this->authCode = $authCode;
		$this->redirectUrl = $redirectUrl;
	}

	/**
	 * [__destruct 析构函数]
	 */
	public function __destruct() {}

	

	/**
	 * [getAccessToken 获取access_token的方法]
	 * @param  string $authCode    [Authorization Code]
	 * @return [string]            [access_token]
	 */
	public function getAccessToken() {

		$reqData = [
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->appId,
			'client_secret' => $this->appKey,
			'code'          => $this->authCode,
			'redirect_uri'  => $this->redirectUrl,
		];

		$ret = self::get(self::ACCESS_TOKE_URL, $reqData);
		parse_str($ret, $parseData);
		if(empty($parseData['access_token'])) {
			self::error(1006);
		}
		return $parseData['access_token'];
	}

	/**
	 * [getUserOpenId 获取QQ用户的唯一标识ID openid]
	 * @param  string $accessToken [令牌]
	 * @return [string]            [access_token]
	 */
	public function getUserOpenId(string $accessToken) {

		$reqData = [
			'access_token' => $accessToken,
		];

		$ret = self::get(self::OPENID_URL, $reqData);
		$paserData = self::parseJsonp($ret);

		if(isset($paserData['error'])) {
			self::error($paserData['error'], $paserData['error_description']);
		}

		if(!isset($paserData['openid'])) {
			self::error(1007);
		}

		return $paserData['openid'];
	}

	/**
	 * [getUserInfo 获取用户基本信息(昵称、头像、性别等)]
	 * @param  string $accessToken [令牌]
	 * @param  string $openId      [唯一标识]
	 */
	public function getUserInfo(string $accessToken, string $openId) {

		$reqData = [
			'access_token'       => $accessToken,
			'oauth_consumer_key' => $this->appId,
			'openid'             => $openId,
		];
		
		$userInfo = self::get(self::USER_INFO_URL, $reqData);
		$userInfoJsonToArr = json_decode($userInfo, true);
		if($userInfoJsonToArr['ret'] != 0) {
			self::error($userInfoJsonToArr['ret'], $userInfoJsonToArr['msg']);
		}
		return $userInfo;
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

	/**
	 * [get get请求]
	 * @param  string      $url     [请求url]
	 * @param  arary       $reqData [请求的数据]
	 * @param  int|integer $timeout [超时数据]
	 * @return [string]             [返回值]
	 */
	public static function get(string $url, array $reqData, int $timeout = 20): string {

		$url .= '?' . http_build_query($reqData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

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