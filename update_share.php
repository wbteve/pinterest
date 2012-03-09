<?php
require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

$index = intval($_REQUEST['index']);
$step = intval($_REQUEST['step']);
if($step == 0)
	$step = 1;

@set_time_limit(0);
if(function_exists('ini_set'))
    ini_set('max_execution_time',0);

if($step == 1)
{
	$limit = 50;
	$list = FDB::fetchAll('SELECT share_id,cache_data FROM '.FDB::table('share')." WHERE share_data IN ('goods','photo','goods_photo') ORDER BY share_id DESC LIMIT ".$index.','.$limit);
	if(count($list) == 0)
	{
		echo "<h1>更新分享图片缓存完成，开始更新专辑图片缓存</h1>";
		echo "<script type=\"text/javascript\">var fun = function(){location.href='update_share.php?step=2&time=".time()."';}; setTimeout(fun,1000);</script>"."\r\n";
		flush();
		ob_flush();
		exit;
	}

	foreach($list as $data)
	{
		$cache_data = fStripslashes(unserialize($data['cache_data']));
		
		$imgs = $cache_data['imgs'];

		include_once fimport('class/image');
		$image = new Image();

		foreach($imgs['all'] as $img)
		{
			$update = array();
			$update['img_width'] = 0;
			$update['img_height'] = 0;

			if(empty($img['server_code']))
			{
				$path = FANWE_ROOT.$img['img'];
				$info = $image->getImageInfo($path);
				if($info['type'] != 'jpg' && $info['type'] != 'jpeg')
				{
					@$image->convertType($path,$path);
				}

				$update['img_width'] = $info[0];
				$update['img_height'] = $info[1];
			}
			else
			{
				$server = FS("Image")->getServer($img['server_code']);
				$args = array();
				if(strpos($img['img'],'./'.$img['server_code'].'/') === FALSE)
					$img['img'] = str_replace('./','./'.$img['server_code'].'/',$img['img']);
				
				$args['img_path'] = $img['img'];
				$server = FS("Image")->getImageUrlToken($args,$server,1);
				$body = FS("Image")->sendRequest($server,'updateimg',true);
				$info = unserialize($body);
				
				$update['img_width'] = $info[0];
				$update['img_height'] = $info[1];
			}

			if($img['type'] == 'g')
			{
				FDB::update('share_goods',$update,"goods_id = ".$img['id']);
			}
			else
			{
				FDB::update('share_photo',$update,"photo_id = ".$img['id']);
			}
			usleep(100);
		}

		FS('Share')->updateShareCache($data['share_id'],'imgs');
		
		echo "更新分享 $data[share_id] 图片缓存成功<br/>";
		flush();
		ob_flush();
		usleep(10);
	}

	echo "<script type=\"text/javascript\">var fun = function(){location.href='update_share.php?step=1&index=".($index + $limit)."&time=".time()."';}; setTimeout(fun,1000);</script>"."\r\n";
	flush();
	ob_flush();
	exit;
}
elseif($step == 2)
{
	$limit = 100;
	$list = FDB::fetchAll('SELECT * FROM '.FDB::table('album').' ORDER BY id DESC LIMIT '.$index.','.$limit);
	if(count($list) == 0)
	{
		echo "<h1>更新专辑图片缓存完成，开始更新商家图片缓存</h1>";
		echo "<script type=\"text/javascript\">var fun = function(){location.href='update_share.php?step=3&time=".time()."';}; setTimeout(fun,1000);</script>"."\r\n";
		flush();
		ob_flush();
		exit;
	}

	foreach($list as $data)
	{
		FS("Album")->updateAlbum($data['id']);
		echo "更新专辑 $data[id] 图片缓存成功<br/>";
		flush();
		ob_flush();
		usleep(100);
	}

	echo "<script type=\"text/javascript\">var fun = function(){location.href='update_share.php?step=2&index=".($index + $limit)."&time=".time()."';}; setTimeout(fun,1000);</script>"."\r\n";
	flush();
	ob_flush();
	exit;
}
elseif($step == 3)
{
	$limit = 100;
	$list = FDB::fetchAll('SELECT * FROM '.FDB::table('shop').' ORDER BY shop_id DESC LIMIT '.$index.','.$limit);

	if(count($list) == 0)
	{
		echo "<h1>更新完成</h1>";
		exit;
	}

	foreach($list as $data)
	{
		$shop_id = $data['shop_id'];
		$shop = array();
		$shop['recommend_count'] = FS("Shop")->getShopUserCount($shop_id);
		$temp = array();
		$temp['tags'] = FS("Shop")->getShopTags($shop_id);
		$temp['goods'] = FS("Shop")->getShopGoods($shop_id);
		$shop['data'] = addslashes(serialize($temp));
		FDB::update('shop',$shop,'shop_id = '.$shop_id);

		echo "更新店铺 $shop_id 图片缓存成功<br/>";
		flush();
		ob_flush();
		usleep(100);
	}

	echo "<script type=\"text/javascript\">var fun = function(){location.href='update_share.php?step=3&index=".($index + $limit)."&time=".time()."';}; setTimeout(fun,1000);</script>"."\r\n";
	flush();
	ob_flush();
	exit;
}
?>