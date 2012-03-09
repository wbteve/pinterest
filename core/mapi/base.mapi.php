<?php
function m_getMConfig(){
	
		global $_FANWE;
		
		//FanweService::instance()->cache->loadCache("m_config");
		$m_config = $_FANWE['cache']['m_config'];		
		if($m_config==false) //测试时，不取缓存
		{
			//init_config_data();//检查初始化数据	
			$m_config = array();			
			$list = FDB::fetchAll("select code,val from ".FDB::table("m_config"));
			foreach($list as $item){
				$m_config[$item['code']] = $item['val'];
			}	
			//新闻公告
			$sql = "select code as title, title as content from ".FDB::table("m_config_list")." where `group` = 4 and is_verify = 1 order by id desc";
			$list = FDB::fetchAll($sql);
			$newslist = array();
			foreach($list as $item){
				
				$newslist[] = array("title"=>$item['title'],"content"=>str_replace("/public/upload/images/",$_FANWE['site_url']."public/upload/images/",$item['content']));
			}
			$m_config['newslist'] = $newslist;
			
	
			//print_r($addrtlist);exit;
			FanweService::instance()->cache->saveCache("m_config",$m_config);			
		}		
		//print_r($m_config);
		return $m_config;
	}
		
function m_emptyTag($string)
{
		if(empty($string))
		return "";
			
		$string = strip_tags(trim($string));
		$string = preg_replace("|&.+?;|",'',$string);
	
		return $string;
}
	
function m_convertUrl($url)
{
		$url = str_replace("&","&amp;",$url);
		return $url;
}
	
function m_display($root)
{
	global $_FANWE;
	$root['user'] = $_FANWE['user'];
	header("Content-Type:text/html; charset=utf-8");
	$r_type = intval($_REQUEST['r_type']);//返回数据格式类型; 0:base64;1;json_encode;2:array
	if ($r_type == 0){
		echo base64_encode(json_encode($root));
	}else if ($r_type == 1){
		print_r(json_encode($root));
	}else if ($r_type == 2){
		print_r($root);
	};
	exit;
}

function m_express($content)
{
	global $_FANWE;
	$express = getCache('m_emotion_express_cache'); //缓存过的表情hash
	if(!$express)
	{
		$express_rs = FDB::fetchAll("select `emotion`,concat('".$_FANWE['site_url']."public/expression/',`type`,'/',`filename`) as fname from ".FDB::table('expression'));
		foreach($express_rs as $k=>$row)
		{
			$express[0][] = $row['emotion'];
			$express[1][] = "<img src='".$row['fname']."' title='".preg_replace("/[\[\]]/",'',$row['emotion'])."' />";
		}
		setCache('m_emotion_express_cache',$express);
	}
	$content = str_replace($express[0],$express[1],$content);

	$parse_user = array();
	preg_match_all("/@([^\f\n\r\t\v@<> ]{2,20}?)(?:\:| )/",$content,$users);
	if(!empty($users[1]))
	{
		$patterns = array();
		$replace = array();
		$users = array_unique($users[1]);
		$arr = array();
		foreach($users as $user)
		{
			if(!empty($user))
			{
				$arr[] = addslashes($user);
			}
		}

		$res = FDB::query('SELECT uid,user_name
			FROM '.FDB::table('user').'
			WHERE user_name '.FDB::createIN($arr));
		while($data = FDB::fetch($res))
		{
			$parse_user[$data['user_name']] = $data['uid'];
		}
	}
	
	$parse_events = array();
	preg_match_all("/#([^\f\n\r\t\v]{1,80}?)#/",$content,$events);
	if(!empty($events[1]))
	{
		$patterns = array();
		$replace = array();
		$events = array_unique($events[1]);
		$arr = array();
		foreach($events as $event)
		{
			if(!empty($event))
			{
				$arr[] = addslashes($event);
			}
		}

		$res = FDB::query('SELECT id,title
			FROM '.FDB::table('event').'
			WHERE title '.FDB::createIN($arr));
		while($data = FDB::fetch($res))
		{
			$parse_events[$data['title']] = $data['id'];
		}
	}

	return array("users"=>$parse_user,"events"=>$parse_events);
}

function m_youhuiItem($item){
	global $_FANWE;
	
	$is_sc = intval($item['is_sc']);
	if ($is_sc > 0) $is_sc = 1;//1:已收藏; 0:未收藏 
	
	if (intval($item['begin_time']) > 0 && intval($item['end_time'])){
		$days = round(($item['end_time']-$item['begin_time'])/3600/24);
		if ($days < 0){
			$ycq = fToDate($item['begin_time'],'Y-m-d').'至'.fToDate($item['end_time'],'Y-m-d').',已过期';
		}else{
			$ycq = fToDate($item['begin_time'],'Y-m-d').'至'.fToDate($item['end_time'],'Y-m-d').',还有'.$days.'天';
		}		
	}else{
		$ycq = '';
	}
	
	return array("id"=>$item['id'],
									"title"=>$item['title'],
									"logo"=> $_FANWE['site_url'].$item['image_1'],
									"logo_1"	=>	$_FANWE['site_url'].$item['image_2'],
									"logo_2"	=>	$_FANWE['site_url'].$item['image_3'],
											"merchant_logo"=> $_FANWE['site_url'].$item['merchant_logo'],
											"create_time"=>$item['create_time'],
											"create_time_format"=>getBeforeTimelag($item['create_time']),
											"xpoint"=>$item['merchant_xpoint'],
											"ypoint"=>$item['merchant_ypoint'],
											"address"=>$item['merchant_api_address'],
											"content"=>$item['content'],
									"is_sc"=>$is_sc,
									"comment_count"=>intval($item['comment_count']),
									"merchant_id"=>intval($item['merchant_id']),
									"begin_time_format"=>fToDate($item['begin_time'],'Y-m-d'),
									"end_time_format"=>fToDate($item['end_time'],'Y-m-d'),
									"ycq"=>$ycq,
									"url"=>$item['url'],
									"city_name"=>$item['city_name']
	
	);
}


?>