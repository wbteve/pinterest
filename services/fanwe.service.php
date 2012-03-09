<?php
//FanWe AMF 服务
class FanweAmfService
{
	function __construct()
	{

	}

	public function getImageServer($server = '',$request = array())
	{
		global $_FANWE;
		$result = array();
		$result['status'] = 0;

		if($_FANWE['uid'] == 0)
			return $result;

		$type = $request['type'];
		switch($type)
		{
			case 'uploadphoto':
				$server = FS("Image")->formatServer($server,'DE');
			break;

			case 'uploadavatar':
				$server = FS("Image")->getServer($_FANWE['user']['server_code']);
			break;
		}
		
		$server = FS("Image")->getImageUrlToken($request['args'],$server);
		if($server === false)
			return $result;

		switch($type)
		{
			case 'uploadphoto':
				$cache_file = getTplCache('services/image/pic_item');
				$args = array();
				$result['html'] = base64_encode(tplFetch("services/image/pic_item",$args,'',$cache_file));
			break;
		}

		$result['status'] = 1;
		$result['max_upload'] = (int)$_FANWE['setting']['max_upload'];
		$result['url'] = $server['url'];
		$result['token'] = $server['token'];
		$result['image_server'] = $server['image_server'];
		return $result;
	}
}
?>