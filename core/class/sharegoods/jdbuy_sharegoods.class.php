<?php
class jdbuy_sharegoods implements interface_sharegoods
{
	public function fetch($url)
	{
        global $_FANWE;
		$id = $this->getID($url);

		if(empty($id))
			return false;

		$key = '360buy_'.$id;
		
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
		$content = getUrlContent("http://www.360buy.com/product/".$id.".html");
		if(empty($content))
			return false;
		
		$content = gbToUTF8($content);
		$content = preg_replace("/[\r\n]/",'',$content);
		@preg_match("/<h1>(.*?)<\/h1>/",$content,$title);
		if(empty($title))
			return false;

		@preg_match("/<div id=\"preview\".*?>.*?<img.*?src=\"(.*?)\".*?jqimg=\"(.*?)\"\/><\/div>/",$content,$img);
		if(empty($img))
			return false;
		
		@preg_match("/<strong class=\"price\"><img.*?src =\"(.*?)\"\/><\/strong>/",$content,$price);
		if(empty($price))
			return false;
		else
		{
			$price = $this->getPrice($price[1]);
			if($price === false)
				return false;
		}

		if(FS("Image")->getIsServer())
		{
			$args = array();
			$args['pic_url'] = $img[2];
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
			$image = copyFile($img[2],"temp",false);
			if($image === false)
				return false;
			$image['server_code'] = '';
		}

		$result['item']['key'] = $key;
		$result['item']['name'] = strip_tags(trim($title[1]));
		$result['item']['price'] = $price;
		$result['item']['img'] = $image['path'];
		$result['item']['server_code'] = $image['server_code'];
		$result['item']['pic_url'] = $img[1];
		$result['item']['url'] = "http://www.360buy.com/product/".$id.".html";
		$unionId = $_FANWE['cache']['business']['jdbuy']['unionId'];
		if(!empty($unionId))
			$result['item']['taoke_url'] = "http://click.union.360buy.com/JdClick/?unionId=".$unionId."&t=4&to=".$result['item']['url'];

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

	public function getKey($url)
	{
		$id = $this->getID($url);
		return '360buy_'.$id;
	}

	public function getPrice($img)
	{
		$dir = './public/upload/temp/'.fToDate(NULL,'Y/m/d/H');
		$file_name = md5(microtime(true)).random('6').'.png';
		makeDir(FANWE_ROOT.$dir);
		$file_path = FANWE_ROOT.$dir."/".$file_name;

		$img_data = getUrlContent($img);
		if(empty($img_data) || @file_put_contents($file_path,$img_data) == 0)
			return false;

		$xs_count = 2;
		$keys = array(
			'7'=> array(
				'011111011000111100011110001111000111100011110001111000110111110'=>0,
				'000110001111000001100000110000011000001100000110000011000111111'=>1,
				'011111011000111100011000001100001100001100001100001100001111111'=>2,
				'011111011000111100011000001100011100000011110001111000110111110'=>3,
				'000011000011100010110010011010001101111111000011000001100000110'=>4,
				'011111101100000110000011111000000110000011110001111000110111110'=>5,
				'001111001100001100000111111011000111100011110001111000110111110'=>6,
				'111111100000110000011000011000001100001100000110000110000011000'=>7,
				'011111011000111100011110001101111101100011110001111000110111110'=>8,
				'011111011000111100011110001111000110111111000001100001100111100'=>9,
			),
			'9'=> array(
				'001111100011000110110000011110000011110000011110000011110000011110000011110000011011000110001111100'=>0,
				'000011000001111000000011000000011000000011000000011000000011000000011000000011000000011000001111110'=>1,
				'011111100110000110110000011000000011000000110000001100000011000000110000001100000011000000111111111'=>2,
				'011111100110000110110000011000000011000000110000111100000000110000000011110000011110000110011111100'=>3,
				'000000110000001110000010110000100110001000110010000110100000110111111111000000110000000110000000110'=>4,
				'011111111011000000011000000011000000011111100000000110000000011000000011110000011110000110011111100'=>5,
				'000111110001100000011000000110000000110111100111000110110000011110000011110000011011000110001111100'=>6,
				'111111111000000011000000110000000110000001100000001100000011000000011000000110000000110000001100000'=>7,
				'001111100011000110110000011110000011011000110001111100011000110110000011110000011011000110001111100'=>8,
				'001111100011000110110000011110000011110000011011000111001111011000000011000000110000001100011111000'=>9,
			),
		);

		$configs = array(
			'1'=>array(
				'word_width'=>9,
				'offset_l'=>20,
				'offset_t'=>4,
				'word_spacing'=>2,
				'd_width'=>5,
			),
			'2'=>array(
				'word_width'=>7,
				'offset_l'=>18,
				'offset_t'=>4,
				'word_spacing'=>2,
				'd_width'=>5,
			),
			'3'=>array(
				'word_width'=>7,
				'offset_l'=>17,
				'offset_t'=>3,
				'word_spacing'=>2,
				'd_width'=>4,
			),
		);

		$res = imagecreatefrompng($file_path);
		$size = getimagesize($file_path);
		$data = array();

		$offset_r = 0;
		for($j = $size[0] - 1; $j >= 0; $j--)
		{	
			$rgb = imagecolorat($res,$j,6);
			$rgbarray = imagecolorsforindex($res, $rgb);
			if($rgbarray['red'] < 125 || $rgbarray['green']<125 || $rgbarray['blue'] < 125)
				break;
			else
				$offset_r++;
		}

		$offset_b = 0;
		for($i= $size[1] - 1; $i >= 0; $i--)
		{
			$rgb = imagecolorat($res,9,$i);
			$rgbarray = imagecolorsforindex($res, $rgb);
			if($rgbarray['red'] < 125 || $rgbarray['green']<125 || $rgbarray['blue'] < 125)
				break;
			else
				$offset_b++;
		}

		$img_width = $size[0];
		if($offset_b == 9)
			$config = $configs[2];
		elseif($offset_b == 10)
			$config = $configs[3];
		else
		{
			$config = $configs[1];
			$offset_b++;
		}
		
		$word_width = $config['word_width'];
		$key = $keys[$word_width];
		$offset_l = $config['offset_l'];
		$offset_t = $config['offset_t'];
		$word_spacing = $config['word_spacing'];
		$d_width = $config['d_width'];

		$no_l = $size[0] - ($xs_count * ($word_width + $word_spacing) + $d_width + $offset_r);
		$no_r = $no_l + $d_width;

		for($i=$offset_t; $i < ($size[1] - $offset_b); $i++)
		{
			$w = 0;
			for($j=$offset_l; $j < ($size[0] - $offset_r); $j++)
			{
				if($j >= $no_l && $j < $no_r)
					continue;
				
				$rgb = imagecolorat($res,$j,$i);
				$rgbarray = imagecolorsforindex($res, $rgb);
				if($rgbarray['red'] < 125 || $rgbarray['green']<125 || $rgbarray['blue'] < 125)
					$data[$i - $offset_t][$w]=1;
				else
					$data[$i - $offset_t][$w]=0;
				$w++;
			}
		}

		$prices = array();
		$length = count($data[0]);
		$height = count($data);
		$count = ($length + $word_spacing) / ($word_width + $word_spacing);
		for($i = 0;$i < $count;$i++)
		{
			$x = $i * ($word_width + $word_spacing);
			for($h = 0;$h < $height;$h++) 
			{
				$temp = '';
				for($w = $x; $w < ($x + $word_width);$w++)
				{
					$prices[$i].= $data[$h][$w];
				}
			}
		}

		$number = '';
		foreach($prices as $price)
		{
			$number .= $key[$price];
		}

		@unlink($file_path);
		return ((float)$number) / 100;
	}
}
?>