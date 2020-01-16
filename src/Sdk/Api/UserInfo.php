<?php 
/**
 * 第三方QQ登录 获取用户的基本信息
 * @link https://github.com/Echo-Mr-Pengw
 * @author  new1024kb
 * @since  2020-01-15
 */
namespace Sdk\Api;

require './OpenId.php';

use Sdk\Api\AccessToken;
use Sdk\Api\OpenId;
use Sdk\Http\Request;
use Sdk\Http\Response;

class UserInfo {

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
	 * [$redirectUrl 成功授权后的回调地址，必须是注册appid时填写的主域名下的地址，建议设置为网站首页或网站的用户中心。注意需要将url进行URLEncode]
	 * @var [string]
	 */
	public $redirectUrl;

	/**
	 * 获取登录用户在QQ空间的信息，包括昵称、头像、性别及黄钻信息的地址
	 */
	const USER_INFO_URL = 'https://graph.qq.com/user/get_user_info';

	const AUTH_CODE_URL = 'https://graph.qq.com/oauth2.0/authorize';

	/**
	 * [__construct 构造函数]
	 * @param string $appId       [appId]
	 * @param string $appKey      [appKey]
	 * @param string $authCode    [Authorization Code]
	 * @param [type] $redirectUrl [成功授权后的回调地址]
	 */
	public function __construct(string $appId, string $appKey, string $redirectUrl) {

		if(empty($appId)) {
			Response::error(1001);
		}

		if(empty($appKey)) {
			Response::error(1002);
		}

		if(empty($redirectUrl)) {
			Response::error(1004);
		}

		$this->appId    = $appId;
		$this->appKey   = $appKey;
		$this->redirectUrl = $redirectUrl;
	}

	/**
	 * [__destruct 析构函数]
	 */
	public function __destruct() {}

	public function getAuthCode() {

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

	/**
	 * [getUserInfo 获取用户基本信息(昵称、头像、性别等)]
	 * @return [type] [description]
	 */
	public function getUserInfo() {
		
		if(isset($_GET['error'])) {
			Response::error($_GET['error'], $_GET['error_description']);
		}

		if(!isset($_GET['code'])) {
			Response::error(1005);
		}

		$authCode = $_GET['code'];
		$getAccessToken = AccessToken::getAccessToken($this->appId, $this->appKey, $authCode, $this->redirectUrl);
		$getOpenId = OpenId::getUserOpenId($getAccessToken);

		$reqData = [
			'access_token'       => $getAccessToken,
			'oauth_consumer_key' => $this->appId,
			'openid'             => $getOpenId,
		];
		
		$userInfo = Request::get(self::USER_INFO_URL, $reqData);
		$userInfoJsonToArr = json_decode($userInfo, true);
		if($userInfoJsonToArr['ret'] != 0) {
			Response::error($userInfoJsonToArr['ret'], $userInfoJsonToArr['msg']);
		}
		return $userInfo;
	}
}

$u = new UserInfo('101846206', '8528b27a58640bd974ec04f14c706af7', 'http://www.new1024kb.com/qq/callback');
$u->getUserInfo();