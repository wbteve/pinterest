<?php
include_once FANWE_ROOT.'sdks/taobao/TopClient.php';
include_once FANWE_ROOT.'sdks/taobao/request/ItemGetRequest.php';
include_once FANWE_ROOT.'sdks/taobao/request/ShopGetRequest.php';
include_once FANWE_ROOT.'sdks/taobao/request/TaobaokeItemsDetailGetRequest.php';
class taobao_sharegoods implements interface_sharegoods
{
	public function fetch($url)
	{
        global $_FANWE;

		$id = $this->getID($url);

		if($id == 0)
			return false;

		$key = 'taobao_'.$id;
		
		$share_goods = FDB::resultFirst('SELECT share_id,goods_id FROM '.FDB::table('share_goods').' 
			WHERE uid = '.$_FANWE['uid']." AND goods_key = '$key'");
		if($share_goods)
		{
			$result['status'] = -1;
			$result['share_id'] = $share_goods['share_id'];
			$result['goods_id'] = $share_goods['goods_id'];
			return $result;
		}

		$client = new TopClient;
		$client->appkey = $_FANWE['cache']['business']['taobao']['app_key'];
		$client->secretKey = $_FANWE['cache']['business']['taobao']['app_secret'];

		$req = new ItemGetRequest;
		$req->setFields("detail_url,title,nick,pic_url,price");
		$req->setNumIid($id);
		$resp = $client->execute($req);

		if(!isset($resp->item))
			return false;

		$result = array();
		$goods = (array)$resp->item;

		if(empty($goods['detail_url']) || empty($goods['pic_url']))
			return false;
		
		if(FS("Image")->getIsServer())
		{
			$args = array();
			$args['pic_url'] = $goods['pic_url'];
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
			$image = copyFile($goods['pic_url'],"temp",false);
			if($image === false)
				return false;
			$image['server_code'] = '';
		}

		$result['item']['key'] = $key;
		$result['item']['name'] = $goods['title'];
		$result['item']['price'] = $goods['price'];
		$result['item']['img'] = $image['path'];
		$result['item']['server_code'] = $image['server_code'];
		$result['item']['pic_url'] = $goods['pic_url'].'_100x100.jpg';
		$result['item']['url'] = $goods['detail_url'];

        $tao_ke_pid = $_FANWE['cache']['business']['taobao']['tk_pid'];
        $shop_click_url = '';
        if(!empty($tao_ke_pid))
        {
            $req = new TaobaokeItemsDetailGetRequest;
            $req->setFields("click_url,shop_click_url");
            $req->setNumIids($id);
            $req->setPid($tao_ke_pid);
            $resp = $client->execute($req);

            if(isset($resp->taobaoke_item_details))
			{
                $taoke = (array)$resp->taobaoke_item_details->taobaoke_item_detail;
                if(!empty($taoke['click_url']))
                    $result['item']['taoke_url'] = $taoke['click_url'];

                if(!empty($taoke['shop_click_url']))
                    $shop_click_url = $taoke['shop_click_url'];
            }
        }

		if(!empty($goods['nick']))
		{
			$req = new ShopGetRequest;
			$req->setFields("sid,nick,pic_path");
			$req->setNick($goods['nick']);
			$resp = $client->execute($req);

			if(isset($resp->shop))
			{
				$shop = (array)$resp->shop;
				$result['shop']['name'] = $shop['nick'];
				$result['shop']['shop_id'] = $shop['sid'];
				$result['shop']['url'] = 'http://shop'.$shop['sid'].'.taobao.com';
				if(!FS("Shop")->getShopExistsByUrl($result['shop']['url']))
				{
					if(!empty($shop['pic_path']))
					{
						if(FS("Image")->getIsServer())
						{
							$args = array();
							$args['pic_url'] = 'http://logo.taobao.com/shop-logo'.$shop['pic_path'];
							$server = FS("Image")->getImageUrlToken($args,'',1);
							$body = FS("Image")->sendRequest($server,'savetemp',true);
							if(!empty($body))
								$image = unserialize($body);
							else
								$image = false;
						}
						else
						{
							$image = copyFile('http://logo.taobao.com/shop-logo'.$shop['pic_path'],"temp",false);
							if($image === false)
								$image['server_code'] = '';
						}

						if($image !== false)
						{
							$result['shop']['logo'] = $image['path'];
							$result['shop']['server_code'] = $image['server_code'];
						}
					}
					
					if(!empty($shop_click_url))
						$result['shop']['taoke_url'] = $shop_click_url;
				}
				else
					unset($result['shop']);
			}
		}

		return $result;
	}

	public function getID($url)
	{
		$id = 0;
		$parse = parse_url($url);
		if(isset($parse['query']))
		{
            parse_str($parse['query'],$params);
			if(isset($params['id']))
				$id = $params['id'];
            elseif(isset($params['item_id']))
                $id = $params['item_id'];
			elseif(isset($params['default_item_id']))
                $id = $params['default_item_id'];
        }
		return $id;
	}

	public function getKey($url)
	{
		$id = $this->getID($url);
		return 'taobao_'.$id;
	}
}
?>