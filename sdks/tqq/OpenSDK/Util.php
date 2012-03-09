<?php

require_once 'Services/JSON.php';
/**
 * OpenSDK工具类
 *
 * 主要做一些通用方法的封装和兼容性工作
 *
 * 依赖：
 * PHP 5 >= 5.1.2, PECL hash >= 1.1 (no need now)
 * 
 * @ignore
 * @author icehu@vip.qq.com
 *
 */

class OpenSDK_Util
{

	/**
	 * fix json_encode
	 *
	 * @see json_encode
	 * @param mix $value
	 * @return string
	 */
	public static function json_encode($value)
	{
		if(function_exists('json_encode'))
		{
			return json_encode($value);
		}
		$jsonObj = new Services_JSON();
        return( $json->encode($value) );
	}
	/**
	 * json_decode
	 *
	 * @see json_decode
	 * @param string $json
	 * @param bool $assoc
	 * @return array|object
	 */
	public static function json_decode($json , $assoc=null)
	{
		if(function_exists('json_decode'))
		{
			return json_decode($json, $assoc);
		}
		$jsonObj = new Services_JSON();
		$use = 0;
		if($assoc)
		{
			//SERVICES_JSON_LOOSE_TYPE	返回关联数组
			$use = 0x10;
		}
        $jsonObj = new Services_JSON($use);
        return $jsonObj->decode($json);
	}

	/**
	 * rfc3986 encode
	 * why not encode ~
	 *
	 * @param string|mix $input
	 * @return string
	 */
	public static function urlencode_rfc3986($input)
    {
        if(is_array($input))
        {
            return array_map( array( __CLASS__ , 'urlencode_rfc3986') , $input);
        }
        else if(is_scalar($input))
		{
			return str_replace('%7E', '~', rawurlencode($input));
		}
		else
		{
			return '';
		}
    }

	/**
	 * fix hash_hmac
	 *
	 * @see hash_hmac
	 * @param string $algo
	 * @param string $data
	 * @param string $key
	 * @param bool $raw_output
	 */
	public static function hash_hmac( $algo , $data , $key , $raw_output = false )
	{
		if(function_exists('hash_hmac'))
		{
			return hash_hmac($algo, $data, $key, $raw_output);
		}

		$algo = strtolower($algo);
		if($algo == 'sha1')
		{
			$pack = 'H40';
		}
		elseif($algo == 'md5')
		{
			$pach = 'H32';
		}
		else
		{
			return '';
		}
		$size = 64;
		$opad = str_repeat(chr(0x5C), $size);
		$ipad = str_repeat(chr(0x36), $size);

		if (strlen($key) > $size) {
			$key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
		} else {
			$key = str_pad($key, $size, chr(0x00));
		}

		for ($i = 0; $i < strlen($key) - 1; $i++) {
			$opad[$i] = $opad[$i] ^ $key[$i];
			$ipad[$i] = $ipad[$i] ^ $key[$i];
		}

		$output = $algo($opad.pack($pack, $algo($ipad.$data)));

		return ($raw_output) ? pack($pack, $output) : $output;
	}
}
