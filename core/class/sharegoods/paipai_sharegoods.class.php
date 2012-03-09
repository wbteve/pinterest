<?php
@set_include_path(FANWE_ROOT.'sdks/paipai/');
require_once 'config.inc.php';
class paipai_sharegoods implements interface_sharegoods
{
	public function fetch($url)
	{
        global $_FANWE;
		
		//QQ号
		define('PAIPAI_API_UIN',$_FANWE['cache']['business']['paipai']['uin']);
		//令牌
		define('PAIPAI_API_TOKEN',$_FANWE['cache']['business']['paipai']['token']);
		//APP_KEY
		define('PAIPAI_API_SECRETKEY',$_FANWE['cache']['business']['paipai']['seckey']);
		define('PAIPAI_API_SPID',$_FANWE['cache']['business']['paipai']['spid']);

		$id = $this->getID($url);

		if(empty($id))
			return false;

		$key = 'paipai_'.$id;
		
		$share_goods = FDB::resultFirst('SELECT share_id,goods_id FROM '.FDB::table('share_goods').' 
			WHERE uid = '.$_FANWE['uid']." AND goods_key = '$key'");
		if($share_goods)
		{
			$result['status'] = -1;
			$result['share_id'] = $share_goods['share_id'];
			$result['goods_id'] = $share_goods['goods_id'];
			return $result;
		}

        $paipaiParamArr = array(
			'uin' => PAIPAI_API_UIN,
			'token' => PAIPAI_API_TOKEN,
			'spid' => PAIPAI_API_SPID,
		);
		
		//API用户参数
		$userParamArr = array(
			'charset' => 'utf-8',
			'format' => 'xml',
			'itemCode' => $id,
		);
		
		$paramArr = $paipaiParamArr + $userParamArr;
		//请求数据
		$goods = Util::getResult($paramArr,'/item/getItem.xhtml');
		
		//解析xml结果
		$goods = Util::getXmlData($goods);
		
		if($goods['errorCode'] > 0)
			return false;

		if(empty($goods['picLink']))
			return false;
		
		if(FS("Image")->getIsServer())
		{
			$args = array();
			$args['pic_url'] = $goods['picLink'];
			$server = FS("Image")->formatServer($_FANWE['request']['image_server'],'DE');
			$server = FS("Image")->getImageUrlToken($args,$server,1);
			$body = FS("Image")->sendRequest($server,'savetemp',true);
			if(empty($body))
				return false;
			$image = unserialize($body);
			$result['image_server'] = $server['image_server'];
		}
		else
		{
			$image = copyFile($goods['picLink'],"temp",false);
			if($image === false)
				return false;
			$image['server_code'] = '';
		}

		$result['item']['key'] = $key;
		$result['item']['name'] = $goods['itemName'];
		$result['item']['price'] = $goods['itemPrice'] / 100;
		$result['item']['img'] = $image['path'];
		$result['item']['server_code'] = $image['server_code'];
		$result['item']['pic_url'] = $goods['picLink'];
		$result['item']['url'] = 'http://auction1.paipai.com/'.$goods['itemCode'];
		
		if(!empty($goods['sellerUin']))
		{
			//API用户参数
			$userParamArr = array(
				'charset' => 'utf-8',
				'format' => 'xml',
				'sellerUin' => $goods['sellerUin'],
			);
			
			$paramArr = $paipaiParamArr + $userParamArr;
			//请求数据
			$shop = Util::getResult($paramArr,'/shop/getShopInfo.xhtml');
			
			//解析xml结果
			$shop = Util::getXmlData($shop);
			if($shop['errorCode'] == 0)
			{
				$result['shop']['name'] = $shop['shopName'];
				$result['shop']['shop_id'] = $shop['sellerUin'];
				$result['shop']['url'] = 'http://shop.paipai.com/'.$shop['sellerUin'];
			}
		}

		return $result;
	}

	public function getID($url)
	{
		$id = '';
		$parse = parse_url($url);
		if(isset($parse['path']))
		{
			$parse = explode('/',$parse['path']);
			$parse = end($parse);
			$parse = explode('-',$parse);
			$id = current($parse);
        }
		return $id;
	}

	public function getKey($url)
	{
		$id = $this->getID($url);
		return 'paipai_'.$id;
	}
}
?>