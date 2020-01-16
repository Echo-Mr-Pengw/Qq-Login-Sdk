<?php
/**
 * Http 请求类
 * @link https://github.com/Echo-Mr-Pengw
 * @author  new1024kb
 * @since  2020-01-15
 */
namespace Sdk\Http;

class Request {
	
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
}
