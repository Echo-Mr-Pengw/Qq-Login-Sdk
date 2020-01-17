<?php 
/**
 * 获取Auth Code 
 */

class AuthCode {

	/**
	 * [$appId 申请QQ登录成功后，分配给网站的appid]
	 * @var [string]
	 */
	public $appId;

	/**
	 * [$redirectUrl 成功授权后的回调地址，必须是注册appid时填写的主域名下的地址，建议设置为网站首页或网站的用户中心。注意需要将url进行URLEncode]
	 * @var [string]
	 */
	public $redirectUrl;

	/**
	 * 获取Auth code的地址
	 */
	const AUTH_CODE_URL = 'https://graph.qq.com/oauth2.0/authorize';

	public function __construct(string $appId, $redirectUrl) {

		$this->appId = $appId;
		$this->redirectUrl = $redirectUrl;
	}

	public function __destruct() {}

	/**
	 * [redirectAuthCodeUrl description]
	 * @return [type] [description]
	 */
	public function headerAuthCodeUrl() {

		$reqData = [
			'response_type' => 'code',
			'client_id'     => $this->appId,
			'redirect_uri'  => urldecode( $this->redirectUrl),
			'state'         => 'new1024kb',
		];

		$getAuthCodeUrl = self::AUTH_CODE_URL . '?' . http_build_query($reqData);
		
		header('location: ' . $getAuthCodeUrl);
		exit;
	}
}