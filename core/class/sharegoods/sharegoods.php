<?php
/**
 * 分享商品插件的接口规范
 * @author hp
 *
 */
interface interface_sharegoods{
	/**
	 * 处理URL，采集
	 * @param $url
	 * 返回结果：
	 * key => 当前商品的唯一标识，不同的模型可根据实际返回唯一的ID
	 * item=> 包含
	 * name:名称
	 * price:价格
	 * img:图片地址
	 * url:商品 地址
	 * shop=>包含
	 * name:商户名称
	 * logo: 商铺LOGO图片
	 * url: 商铺地址
	 * shop_id: 商铺的ID，该ID为在相关站点下的可识别主键
	 * 
	 */
	function fetch($url);
	
	/**
	 * 获取该商品的标识，用于检测是否已经采集
	 */
	function getKey($url);
}

function curl_get_contents($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
	curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
}
?>