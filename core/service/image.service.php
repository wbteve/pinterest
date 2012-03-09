<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * image.service.php
 *
 * 图片服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class ImageService
{
	public function getIsServer()
	{
		static $is_server = NULL;
		if($is_server === NULL)
		{
			global $_FANWE;
			if(!isset($_FANWE['cache']['image_servers']))
				FanweService::instance()->cache->loadCache('image_servers');
			
			if(count($_FANWE['cache']['image_servers']['active']) == 0)
				$is_server = false;
			else
				$is_server = true;
		}
		return $is_server;
	}

	public function formatServer($server,$type = 'EN')
	{
		if(empty($server))
			return false;

		if($type == 'DE')
			return unserialize(authcode(base64_decode($server),'DECODE'));
		else
			return base64_encode(authcode(serialize($server),'ENCODE'));
	}

	public function getImageArgs($args)
	{
		global $_FANWE;
		if(!isset($_FANWE['cache']['image_sizes']))
			FanweService::instance()->cache->loadCache('image_sizes');

		$args['sizes'] = $_FANWE['cache']['image_sizes'];
		$args['waters'] = false;
		$water_image = $_FANWE['setting']['water_image'];
		if(!empty($water_image) && file_exists(FANWE_ROOT.$water_image))
		{
			$args['waters'] = array();
			$args['waters']['image'] = $_FANWE['site_url'].$water_image;
			$args['waters']['mark'] = (int)$_FANWE['setting']['water_mark'];
			$args['waters']['alpha'] = (int)$_FANWE['setting']['water_alpha'];
			$args['waters']['position'] = (int)$_FANWE['setting']['water_position'];
		}
	}

	public function getImageUrl($url,$is_path = 0)
	{
		if(empty($url))
			return '';

		global $_FANWE;
		static $patterns = NULL,$replace = NULL;
			
		if(ImageService::getIsServer() && $patternss === NULL)
		{
			if(!isset($_FANWE['cache']['image_servers']))
				FanweService::instance()->cache->loadCache('image_servers');
			foreach($_FANWE['cache']['image_servers']['all'] as $server)
			{
				$patterns[] = './'.$server['code'].'/';
				$replace[] = $server['url'];
			}
		}

		if(strpos($url,'./public/') === FALSE)
			$url = str_replace($patterns,$replace,$url);
		elseif($is_path == 1)
			$url = str_replace('/./','/',FANWE_ROOT.$url);
		elseif($is_path == 2)
			$url = str_replace('/./','/',$_FANWE['site_url'].$url);
		return $url;
	}

	public function getServer($code = '')
	{
		global $_FANWE;
		if(!isset($_FANWE['cache']['image_servers']))
			FanweService::instance()->cache->loadCache('image_servers');
		
		if(count($_FANWE['cache']['image_servers']['all']) == 0)
			return false;	
		
		if(!empty($code))
			return $_FANWE['cache']['image_servers']['all'][$code];

		if(count($_FANWE['cache']['image_servers']['active']) > 1)
		{
			$server_code = FDB::resultFirst('SELECT code FROM '.FDB::table('image_servers').' WHERE status = 1 ORDER BY upload_count ASC');
			if($server_code)
				return $_FANWE['cache']['image_servers']['active'][$server_code];
			else
				return false;
		}
		else
		{
			return current($_FANWE['cache']['image_servers']['active']);
		}
		return false;
	}

	public function setServerUploadCount($code)
	{
		FDB::query('UPDATE '.FDB::table('image_servers').' SET upload_count = upload_count + 1 WHERE code = \''.$code."'");
	}
	
	public function getImageUrlToken($args = array(),$server = '',$is_system = 0)
	{
		global $_FANWE;
		if($_FANWE['uid'] > 0 || $is_system)
		{
			if(empty($server))
				$server = ImageService::getServer();
				
			if(empty($server))
				return false;
			
			$token = array(
				'code' => $server['code'],
				'uid' => $_FANWE['uid'],
				'max_upload'=>(int)$_FANWE['setting']['max_upload'],
				'saltkey' => $_FANWE['cookie']['saltkey'],
				'system' => $is_system,
				'ip' => $_FANWE['client_ip'],
				'time' => TIME_UTC,
				'args' => $args
			);
			
			$token = serialize($token);
			$authkey = md5($_FANWE['config']['security']['authkey'].$server['code']);
			$result = array();
			$result['code'] = $server['code'];
			$result['url'] = $server['url'];
			$result['host'] = $server['host'];
			$result['host_port'] = $server['host_port'];
			$result['port'] = $server['port'];
			$result['path'] = $server['path'];
			$result['token'] = rawurlencode(authcode($token,'ENCODE',$authkey));
			$result['image_server'] = ImageService::formatServer($server);
			return $result;
		}
		else
			return false;
	}
	
	public function sendRequest($server,$type,$is_sync = false)
	{
		if(empty($server))
			return false;
			
		$crlf = '';
        if (strtoupper(substr(PHP_OS, 0, 3) === 'WIN'))
            $crlf = "\r\n";
        elseif (strtoupper(substr(PHP_OS, 0, 3) === 'MAC'))
            $crlf = "\r";
        else
            $crlf = "\n";
			
		$params = "token=".rawurlencode($server['token']);
		$timeout = 5;
		if($is_sync)
			$timeout = 60;

		$fp=fsockopen($server['host'],$server['port'],&$errno,&$errstr,$timeout);
		if($fp)
		{
			$request = "POST ".$server['path'].$type.".php HTTP/1.0".$crlf;
			$request .= "Host: ".$server['host_port'].$crlf;
			$request .= "Content-Type: application/x-www-form-urlencoded".$crlf;
			$request .= 'Content-Length: '.strlen($params).$crlf;
			$request .= "Connection: Close".$crlf.$crlf;

			$request .= $params;

			if(!@fwrite($fp,$request))
				return false;
			
			$http_response = '';
			while(!feof($fp))
			{
				if(!$is_sync)
				{
					fgets($fp,128);
					break;
				}
				else
				{
					$http_response .= fgets($fp);
				}
			}
			fclose($fp);
			
			if($is_sync)
			{
				$separator = '/\r\n\r\n|\n\n|\r\r/';
				list($http_header,$http_body) = preg_split($separator,$http_response,2);
				return $http_body;
			}

			return true;
		}
		else
			return false;
	}
	/**
	 * 保存图片
	 * @param array $key $_FILES 中的键名 为空则保存 $_FILES 中的所有图片
	 * @param string $dir 保存的目录 为空则保存到临时目录
	 * @param bool $is_thumb 是否缩略图片
	 * @param array $whs 缩略图大小信息 为空则取后台设置,并返回 大图键名big 小图键名small
	 	可生成多个缩略图
		数组 参数1 为宽度，
			 参数2为高度，
			 参数3为处理方式:0(缩放,默认)，1(剪裁)，
			 参数4为是否水印 默认为 0(不生成水印)
	 	array(
			'thumb1'=>array(300,300,0,0),
			'thumb2'=>array(100,100,0,0),
			...
		)，
	 * @param bool $is_delete_origin 是否删除原图(当有缩略图时，此设置才生效)
	 * @param bool $is_water 是否水印
	 * @return array
	 	如果只有一个图片，则返回
		array(
			'name'=>图片名称，
			'url'=>原图web路径，
			'path'=>原图物理路径，
			有略图时
			'thumb'=>array(
				'thumb1'=>array('url'=>web路径,'path'=>物理路径),
				'thumb2'=>array('url'=>web路径,'path'=>物理路径),
				...
			)
		)
		如果有多个图片，则返回(key 为 $_FILES 中的键名)
		array(
			'key'=>array(
				'name'=>图片名称，
				'url'=>原图web路径，
				'path'=>原图物理路径，
				有略图时
				'thumb'=>array(
					'thumb1'=>array('url'=>web路径,'path'=>物理路径),
					'thumb2'=>array('url'=>web路径,'path'=>物理路径),
					...
				)
			)
			....
		)
	 */
	public function save($key='',$dir='temp',$is_thumb=false,$whs=array(),$is_delete_origin = false,$is_water = false)
	{
		global $_FANWE;
		include_once fimport('class/image');
		$image = new Image();
		if(intval($_FANWE['setting']['max_upload']) > 0)
			$image->max_size = intval($_FANWE['setting']['max_upload']);

		$list = array();

		if(empty($key))
		{
			foreach($_FILES as $fkey=>$file)
			{
				$list[$fkey] = false;
				$image->init($file,$dir);
				if($image->save())
				{
					$list[$fkey] = array();
					$list[$fkey]['url'] = $image->file['target'];
					$list[$fkey]['path'] = $image->file['local_target'];
					$list[$fkey]['name'] = $image->file['prefix'];
				}
			}
		}
		else
		{
			$list[$key] = false;
			$image->init($_FILES[$key],$dir);
			if($image->save())
			{
				$list[$key] = array();
				$list[$key]['url'] = $image->file['target'];
				$list[$key]['path'] = $image->file['local_target'];
				$list[$key]['name'] = $image->file['prefix'];
			}
		}

		$water_image = FANWE_ROOT . $_FANWE['setting']['water_image'];
		$water_mark = intval($_FANWE['setting']['water_mark']);
		$alpha = intval($_FANWE['setting']['water_alpha']);
		$place = intval($_FANWE['setting']['water_position']);

		if($is_thumb)
		{
			if(empty($whs))
			{
				$big_width = intval($_FANWE['setting']['big_width']);
				$big_height = intval($_FANWE['setting']['big_height']);
				$small_width = intval($_FANWE['setting']['small_width']);
				$small_height = intval($_FANWE['setting']['small_height']);
				$thumb_type = intval($_FANWE['setting']['auto_gen_image']);

				$whs = array(
					'big'=>array($big_width,$big_height,$thumb_type,$water_mark),
					'small'=>array($small_width,$small_height,1,0),
				);
			}
		}

		foreach($list as $lkey=>$item)
		{
			if($is_thumb)
			{
				foreach($whs as $tkey=>$wh)
				{
					$list[$lkey]['thumb'][$tkey]['url'] = false;
					$list[$lkey]['thumb'][$tkey]['path'] = false;

					if($wh[0] > 0 || $wh[1] > 0)
					{
						$thumb_bln = false;
						$thumb_type = isset($wh[2]) ? intval($wh[2]) : 0;
						if($thumb = $image->thumb($item['path'],$wh[0],$wh[1],$thumb_type))
						{
							$thumb_bln = true;
							$list[$lkey]['thumb'][$tkey]['url'] = $thumb['url'];
							$list[$lkey]['thumb'][$tkey]['path'] = $thumb['path'];
							if(isset($wh[3]) && intval($wh[3]) > 0)
								$image->water($list[$lkey]['thumb'][$tkey]['path'],$water_image,$alpha, $place);
						}
					}
				}

				if($is_delete_origin && $thumb_bln)
				{
					@unlink($item['path']);
					$list[$lkey]['url'] = false;
					$list[$lkey]['path'] = false;
				}
			}

			if($is_water)
			{
				$image->water($item['path'],$water_image,$alpha, $place);
			}
		}
		
		if($key != '')
			return $list[$key];
		else
			return $list;
	}
}
?>