<?php
class vancl_sharegoods implements interface_sharegoods
{
	public function fetch($url)
	{
        global $_FANWE;
		
		if(strpos($url,'vjia.com') !== false)
		{
			return $this->vjiaFetch($url);
		}

		$id = $this->getID($url);
		if(empty($id))
			return false;

		$key = 'vancl_'.$id;
		
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
		$content = getUrlContent("http://item.vancl.com/".$id.".html");
		if(empty($content))
			return false;

		$content = preg_replace("/[\r\n]/",'',$content);
		@preg_match("/<span id=\"styleinfo\".*?>(.*?)<\/span>/",$content,$title);
		if(empty($title))
			return false;

		@preg_match("/<li id=\"onlickImg\".*?>.*?<img src=\"(.*?)\".*?\/><\/li>/",$content,$img);
		if(empty($img))
			return false;
		
		@preg_match("/<div class=\"cuxiaoPrice\".*?>.*?￥<strong>(.*?)<\/strong>.*?<\/div>/u",$content,$price);
		if(empty($price))
			return false;

		if(FS("Image")->getIsServer())
		{
			$args = array();
			$args['pic_url'] = str_replace('/small/','/big/',$img[1]);
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
			$image = copyFile(str_replace('/small/','/big/',$img[1]),"temp",false);
			if($image === false)
				return false;
			$image['server_code'] = '';
		}

		$result['item']['key'] = $key;
		$result['item']['name'] = strip_tags(trim($title[1]));
		$result['item']['price'] = (float)$price[1];
		$result['item']['img'] = $image['path'];
		$result['item']['server_code'] = $image['server_code'];
		$result['item']['pic_url'] = str_replace('/small/','/mid/',$img[1]);
		$result['item']['url'] = "http://item.vancl.com/".$id.".html";
		$Source = $_FANWE['cache']['business']['vancl']['Source'];
		if(!empty($Source))
			$result['item']['taoke_url'] = $result['item']['url']."?Source=".$Source;

		return $result;
	}

	public function vjiaFetch($url)
	{
        global $_FANWE;
	
		$urls = $this->getVjiaID($url);
		if(empty($urls['id']))
			return false;

		$key = 'vjia_'.$urls['id'];
		
		$share_goods = FDB::resultFirst('SELECT share_id,goods_id FROM '.FDB::table('share_goods').' 
			WHERE uid = '.$_FANWE['uid']." AND goods_key = '$key'");
		if($share_goods)
		{
			$result['status'] = -1;
			$result['share_id'] = $share_goods['share_id'];
			$result['goods_id'] = $share_goods['goods_id'];
			return $result;
		}

		$url = $urls['url'];

		//请求数据
		$content = getUrlContent($url);
		if(empty($content))
			return false;

		$content = preg_replace("/[\r\n]/",'',$content);
		@preg_match("/<li class=\"title\">(.*?)<\/li>/",$content,$title);

		if(empty($title))
			return false;

		@preg_match("/<div class=\"sp-bigImg\">.*?<img.*?src=\"(.*?)\".*?\/>.*?<\/div>/",$content,$img);
		if(empty($img))
			return false;
		
		@preg_match("/<span id=\"SpecialPrice\">(.*?)<\/span>/",$content,$price);
		if(empty($price))
			return false;

		if(FS("Image")->getIsServer())
		{
			$args = array();
			$args['pic_url'] = str_replace('/mid/','/big/',$img[1]);
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
			$image = copyFile(str_replace('/mid/','/big/',$img[1]),"temp",false);
			if($image === false)
				return false;
			$image['server_code'] = '';
		}

		$result['item']['key'] = $key;
		$result['item']['name'] = strip_tags(trim($title[1]));
		$result['item']['price'] = (float)$price[1];
		$result['item']['img'] = $image['path'];
		$result['item']['server_code'] = $image['server_code'];
		$result['item']['pic_url'] = $img[1];
		$result['item']['url'] = $url;
		$Source = $_FANWE['cache']['business']['vancl']['Source'];
		if(!empty($Source))
			$result['item']['taoke_url'] = $result['item']['url']."?Source=".$Source;

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
			$parse = explode('.',$parse);
			$id = current($parse);
        }
		return $id;
	}

	public function getVjiaID($url)
	{
		$id = '';
		$parse = parse_url($url);

		if(isset($parse['path']))
		{
			$url = $parse['scheme'].'://'.$parse['host'].$parse['path'];
			$parse = explode('/',$parse['path']);
			$parse = end($parse);
			$parse = explode('.',$parse);
			$id = current($parse);
			
        }

		return array('id'=>$id,'url'=>$url);
	}

	public function getKey($url)
	{
		$id = $this->getID($url);
		return 'vancl_'.$id;
	}
}
?>