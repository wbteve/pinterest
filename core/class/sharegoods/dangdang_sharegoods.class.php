<?php
class dangdang_sharegoods implements interface_sharegoods
{
	public function fetch($url)
	{
        global $_FANWE;

		$id = $this->getID($url);

		if(empty($id))
			return false;

		$key = 'dangdang_'.$id;
		
		$share_goods = FDB::resultFirst('SELECT share_id,goods_id FROM '.FDB::table('share_goods').' 
			WHERE uid = '.$_FANWE['uid']." AND goods_key = '$key'");
		if($share_goods)
		{
			$result['status'] = -1;
			$result['share_id'] = $share_goods['share_id'];
			$result['goods_id'] = $share_goods['goods_id'];
			return $result;
		}

		//请求数据
		$content = getUrlContent("http://product.dangdang.com/product.aspx?product_id=".$id);
		if(empty($content))
			return false;

		$content = gbToUTF8($content);
		$content = preg_replace("/[\r\n]/",'',$content);
		@preg_match("/<h1>(.*?)<\/h1>/",$content,$title);
		if(empty($title))
			return false;

		@preg_match("/var oldimage \= '(.*?)';/",$content,$img);
		if(empty($img))
			return false;
		
		@preg_match("/<span class=\"promotions_price_d\">￥<b>(.*?)<\/b><\/span>/u",$content,$price);
		if(empty($price))
		{
			@preg_match("/<span.*?id=\"salePriceTag\">￥(.*?)<\/span>/u",$content,$price);
			if(empty($price))
				return false;
			else
				$price = (float)$price[1];
		}
		else
			$price = (float)$price[1];

		if(FS("Image")->getIsServer())
		{
			$args = array();
			$args['pic_url'] = $img[1];
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
			$image = copyFile($img[1],"temp",false);
			if($image === false)
				return false;
			$image['server_code'] = '';
		}

		$result['item']['key'] = $key;
		$result['item']['name'] = strip_tags(str_replace('[当当自营]','',$title[1]));
		$result['item']['price'] = $price;
		$result['item']['img'] = $image['path'];
		$result['item']['server_code'] = $image['server_code'];
		$result['item']['pic_url'] = $img[1];
		$result['item']['url'] = "http://product.dangdang.com/product.aspx?product_id=".$id;
		$from = $_FANWE['cache']['business']['dangdang']['from'];
		if(!empty($from))
			$result['item']['taoke_url'] = "http://union.dangdang.com/transfer.php?from=".$from."&ad_type=10&sys_id=1&backurl=".$result['item']['url'];

		@preg_match("/<div class=\"legend\"><a href=\"http:\/\/shop\.dangdang\.com\/(.*?)\".*?>(.*?)<\/a><a.*?>.*?<\/a><\/div>/",$content,$shop);
		if(!empty($shop))
		{
			$result['shop']['name'] = $shop[2];
			$result['shop']['url'] = "http://shop.dangdang.com/".$shop[1];
			if(!empty($from))
				$result['shop']['taoke_url'] = "http://union.dangdang.com/transfer.php?from=".$from."&ad_type=10&sys_id=1&backurl=".$result['shop']['url'];

			if(!FS("Shop")->getShopExistsByUrl($result['shop']['url']))
			{
				$content = getUrlContent($result['shop']['url']);
				if(!empty($content))
				{
					$content = preg_replace("/[\r\n]/",'',$content);
					@preg_match("/<dl.*?id=\"hslice_shop_basic_info_".$shop[1]."\">.*?<dt><a.*?><img src=\"(.*?)\".*?\/><\/a><\/dt>/",$content,$shop_img);
					if(!empty($shop_img))
					{
						if(FS("Image")->getIsServer())
						{
							$args = array();
							$args['pic_url'] = $shop_img[1];
							$server = FS("Image")->getImageUrlToken($args,'',1);
							$body = FS("Image")->sendRequest($server,'savetemp',true);
							if(!empty($body))
								$image = unserialize($body);
							else
								$image = false;
						}
						else
						{
							$image = copyFile($shop_img[1],"temp",false);
							if($image === false)
								$image['server_code'] = '';
						}

						if($image !== false)
						{
							$result['shop']['logo'] = $image['path'];
							$result['shop']['server_code'] = $image['server_code'];
						}
					}
				}
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
			if(isset($params['product_id']))
				$id = $params['product_id'];
            elseif(isset($params['id']))
                $id = $params['id'];
        }
		return $id;
	}

	public function getKey($url)
	{
		$id = $this->getID($url);
		return 'dangdang_'.$id;
	}
}
?>