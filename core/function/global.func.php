<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * global.func
 *
 * 公共函数
 *
 * @package function
 * @author awfigq <awfigq@qq.com>
 */
function getPhpSelf()
{
	$php_self = '';
	$script_name = basename($_SERVER['SCRIPT_FILENAME']);
	if(basename($_SERVER['SCRIPT_NAME']) === $script_name)
		$php_self = $_SERVER['SCRIPT_NAME'];
	else if(basename($_SERVER['PHP_SELF']) === $script_name)
		$php_self = $_SERVER['PHP_SELF'];
	else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $script_name)
		$php_self = $_SERVER['ORIG_SCRIPT_NAME'];
	else if(($pos = strpos($_SERVER['PHP_SELF'],'/'.$script_name)) !== false)
		$php_self = substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$script_name;
	else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT']) === 0)
		$php_self = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
	else
		return false;
	return $php_self;
}

/**
 * 获取引用文件路径
 * @param string $file_name 文件名称
 * @param string $folder 所在目录(默认为空)
 * @return string
 */
function fimport($file_name, $folder = '')
{
	global $_FANWE;
	static $sufix = array(
		'module'=>'.module',
		'service'=>'.service',
		'class'=>'.class',
		'function' => '.func',
		'include' => '.inc',
		'language' => '.lang',
		'cache' => '.cache',
		'dynamic'=>'.dynamic',
	);

	$file_name = strtolower($file_name);
	$file_path = FANWE_ROOT.'./core';
	if(strstr($file_name, '/'))
	{
		list($pre, $name) = explode('/', $file_name);

		$insert = '';
		if($pre == 'language')
			$insert = $_FANWE['config']['default_lang'].'/';

		return "{$file_path}/{$pre}/".$insert.(empty($folder) ? "" : $folder . "/")."{$name}".$sufix[$pre].".php";
	}
	else
	{
		return "{$file_path}/".(empty($folder) ? "" : $folder . "/")."{$file_name}.php";
	}
}

/**
 * 获取页面显示操作类
 * @param string $module 类名
 * @return object
 */
function FM($module)
{
	static $modules = array();
	if($modules[$module] === NULL)
	{
		require fimport("module/".strtolower($module));
		$m = ucfirst($module)."Module";
		$modules[$module] = new $m();
		unset($m);
	}
	return $modules[$module];
}

/**
 * 获取服务类
 * @param string $service 类名
 * @return object
 */
function FS($service)
{
	static $services = array();
	if($services[$service] === NULL)
	{
		require_once fimport("service/".strtolower($service));
		$s = ucfirst($service)."Service";
		$services[$service] = new $s();
		unset($s);
	}

	return $services[$service];
}

/**
 * 页面路径处理
 * @param string $type 页面
 * @param array $args 参数
 * @return string
 */
function FU($type,$args,$is_full = false)
{
	global $_FANWE;
	static $is_rewrite = NULL,$site_url = NULL,$url_lists = array(),$url_flists = array();
	
    if ($is_rewrite === NULL)
        $is_rewrite = intval($_FANWE['setting']['url_model']);

	if ($site_url === NULL)
        $site_url = $_FANWE['site_root'];

	$depr = '/';

	$url = $site_url;
	if($is_full)
	{
		$url = $_FANWE['site_url'];
		$site_url = $_FANWE['site_url'];
	}

	$apps = explode('/',$type);
	$module = $apps[0];
	$action = isset($apps[1]) ? $apps[1] : 'index';
	$type = $module.'/'.$action;

	$url_key = $type.'_'.md5(http_build_query($args));
	if($is_full)
	{
		if(isset($url_flists[$url_key]))
			return $url_flists[$url_key];
	}
	else
	{
		if(isset($url_lists[$url_key]))
			return $url_lists[$url_key];
	}
	
	$query = '';
	if($is_rewrite == 0)
	{
		$query = http_build_query($args);
		if(!empty($query))
			$query = '&'.$query;
	}

	if($is_rewrite == 0)
	{
		$url .= $module.'.php?action='.$action.$query;
	}
	else
	{
		$params = array();
		switch($type)
		{
			case 'club/index':
			case 'ask/index':
			case 'event/index':
			case 'invite/index':
				$search = array('/index');
				$replace = array('');
			break;
			
			case 'link/index':
				$module = "links";
				$search = array('/index');
				$replace = array('');
			break;
			
			case 'daren/index':
				$params = array('page' => 0);
				$search = array('/index','page/');
				$replace = array('','');
			break;
			
			case 'daren/all':
				$params = array('page' => 0);
				$search = array('page/');
				$replace = array('');
			break;

			case 'club/forum':
				$params = array('fid' => 0,'sort' => '','page' => 0);
				$search = array('forum/','fid/','sort/','page/');
				$replace = array('','','','');
			break;

			case 'club/best':
				$params = array('fid' => 0,'sort' => '','page' => 0);
				$search = array('fid/','sort/','page/');
				$replace = array('','','');
			break;

			case "club/newtopic":
				$search = array('fid/');
				$replace = array('');
			break;

			case "club/detail":
				$params = array('tid' => 0,'page' => 0);
				$search = array('tid/','page/');
				$replace = array('','');
			break;

			case 'ask/forum':
				$params = array('aid' => 0,'type' => '','page' => 0);
				$search = array('forum/','aid/','type/','page/');
				$replace = array('','','','');
			break;

			case "ask/newtopic":
				$search = array('aid/');
				$replace = array('');
			break;

			case "ask/detail":
				$params = array('tid' => 0,'page' => 0);
				$search = array('tid/','page/');
				$replace = array('','');
			break;

			case 'book/cate':
			case 'book/shopping':
			case 'book/search':
				$params = array('cate' => '','sid' => 0,'sort'=>'','tag' => '','page' => 0);
				$search = array('cate/','sid/','sort/','tag/','page/');
				$replace = array('','s','','','');
				$url =  str_replace($search,$replace,$url);
			break;

			case 'book/dapei':
			case 'book/look':
				$params = array('sid' => 0,'sort'=>'','tag' => '','page' => 0);
				$search = array('sid/','sort/','tag/','page/');
				$replace = array('s','','','');
				$url =  str_replace($search,$replace,$url);
			break;

            case 'style/index':
				$params = array('sort'=>'','tag' => '','page' => 0);
				$search = array('/index','sort/','tag/','page/');
				$replace = array('','','','');
				$url =  str_replace($search,$replace,$url);
			break;
			case 'event/detail':
				$params = array('detail'=>'','id'=>'','page' => 0);
				$search = array('/detail','/id','/page');
				$replace = array('','','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'event/list':
				$params = array('type'=>'','order'=>'','page' => 0);
				$search = array('/type','/order','/page');
				$replace = array('','','');
				$url =  str_replace($search,$replace,$url);
			break;

            case 'adv/show':
				$params = array('id'=>'');
				$search = array('/show','id/');
				$replace = array('','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'second/index':
				$params = array('sid'=>0,'cid' => 0,'page' => 0);
				$search = array('/index','sid/','cid/','page/');
				$replace = array('','s','c','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'album/index':
				$params = array('sort'=>'','page' => 0);
				$search = array('/index','sort/','page/');
				$replace = array('','','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'album/category':
				$params = array('id'=>0,'sort'=>'','page' => 0);
				$search = array('id/','sort/','page/');
				$replace = array('c','','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'album/show':
				$args['aid'] = $args['id'];
				unset($args['id']);
				$params = array('aid'=>0,'sid'=>0,'type' => 0,'page' => 0);
				$search = array('aid/','sid/','type/','page/');
				$replace = array('a','s','t','');
				$url =  str_replace($search,$replace,$url);
			break;

			case 'album/edit':
				$params = array('id'=>0);
				$search = array('id/');
				$replace = array('');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'shop/index':
				$params = array('cid' => 0,'page' => 0);
				$search = array('/index','cid/','page/');
				$replace = array('','c','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'shop/show':
				$params = array('id' => 0,'page' => 0);
				$search = array('id/','page/');
				$replace = array('s','');
				$url =  str_replace($search,$replace,$url);
			break;
			
			case 'exchange/index':
				$params = array('page' => 0);
				$search = array('/index','page/');
				$replace = array('','');
				$url =  str_replace($search,$replace,$url);
			break;
		}

		if(!empty($params))
			$args = array_merge($params, $args);

		foreach($args as $var=>$val)
		{
			if($var == 'page' && $val == '0')
				$val = '';

			if($val != '')
				$query .= $depr.$var.$depr.$val;
		}

		$url .= $module.$depr.$action.$query;
		if(!empty($search))
			$url = str_replace($search,$replace,$url);

		switch($module)
		{
			case 'index':
				$url = $site_url;
			break;

			case 'u':
				if(!isset($args['uid']))
					$args['uid'] = $_FANWE['uid'];

				if($action == 'all')
					$url = $site_url.$module.$depr.$action;
				elseif($action == 'msgview')
                {
					$url = str_replace('/page/','/',$url);
				}
				else
				{
					if($action == 'index')
					{
						if($args['uid'] == $_FANWE['uid'])
							$url = $site_url.'me';
						else
							$url = $site_url.$module.$depr.$args['uid'];
					}
					else
						$url = $site_url.$module.$depr.$args['uid'].$depr.$action;
					
					if($action == 'album')
					{
						if(isset($args['type']) && $args['type'] != '')
						{
							$url.= $depr.'t'.$args['type'];
						}
					}
					elseif($action == 'exchange')
					{
						if(isset($args['status']) && $args['status'] != '')
							$url.= $depr.'s'.$args['status'];
					}
					else
					{
						if(isset($args['type']) && $args['type'] != '')
							$url.= $depr.$args['type'];
	
						if(isset($args['sort']) && $args['sort'] != '')
							$url.= $depr.$args['sort'];
					}
					
					if(isset($args['page']) && $args['page'] != '0')
						$url.= $depr.$args['page'];
				}
			break;

			case 'note':
				if($action == 'index')
					$url = $site_url.$module.$depr.$args['sid'];
				else
					$url = $site_url.$module.$depr.$args['sid'].$depr.$action.$depr.$args['id'];

				if(isset($args['page']) && $args['page'] != '0')
					$url.= $depr.$args['page'];
			break;
		}
	}

    if($type == 'tgo/index')
		$url = $site_url.'tgo.php?url='.base64_encode($args['url']);
	
	if($is_full)
		$url_flists[$url_key] = $url;
	else
		$url_lists[$url_key] = $url;
	return $url;
}

/**
 * 页面重写参数处理
 * @param array $keys 键值对
 * @return void
 */
function getRewriteArgs($keys)
{
	global $_FANWE;
	$args = trim($_FANWE['request']['args']);
	foreach($keys as $key)
	{
		preg_match("/$key-(.+?)(?:$|-)/is",$args,$value);
		if(count($value) > 1)
		{
			$_FANWE['request'][$key] = $value[1];
			if($key == 'page')
				$_FANWE['page'] = $value[1];
		}
	}
	unset($_FANWE['request']['args']);
}

/**
 * 分页处理
 * @param string $type 所在页面
 * @param array  $args 参数
 * @param int $total_count 总数
 * @param int $page 当前页
 * @param int $page_size 分页大小
 * @param string $url 自定义路径
 * @param int $offset 偏移量
 * @return array
 */
function buildPage($type,$args,$total_count,$page = 1,$page_size = 0,$url='',$offset = 5)
{
	global $_FANWE;

	$pager['total_count'] = intval($total_count);
	$pager['page'] = $page;
	$pager['page_size'] = ($page_size == 0) ? ($_FANWE['setting']['page_listrows'] > 0 ? $_FANWE['setting']['page_listrows'] : 20) : $page_size;
	/* page 总数 */
	$pager['page_count'] = ($pager['total_count'] > 0) ? ceil($pager['total_count'] / $pager['page_size']) : 1;

	/* 边界处理 */
	if ($pager['page'] > $pager['page_count'])
		$pager['page'] = $pager['page_count'];

	$pager['limit'] = ($pager['page'] - 1) * $pager['page_size'] . "," . $pager['page_size'];

	$page_prev  = ($pager['page'] > 1) ? $pager['page'] - 1 : 1;
	$page_next  = ($pager['page'] < $pager['page_count']) ? $pager['page'] + 1 : $pager['page_count'];
	$pager['prev_page'] = $page_prev;
	$pager['next_page'] = $page_next;

	if (!empty($url))
	{
		$pager['page_first'] = $url . 1;
		$pager['page_prev']  = $url . $page_prev;
		$pager['page_next']  = $url . $page_next;
		$pager['page_last']  = $url . $pager['page_count'];
	}
	else
	{
		$args['page'] = '_page_';
		if(!empty($type))
			$page_url = FU($type,$args);
		else
			$page_url = 'javascript:;';

		$pager['page_first'] = str_replace('_page_',1,$page_url);
		$pager['page_prev']  = str_replace('_page_',$page_prev,$page_url);
		$pager['page_next']  = str_replace('_page_',$page_next,$page_url);
		$pager['page_last']  = str_replace('_page_',$pager['page_count'],$page_url);
	}

	$pager['page_nums'] = array();

	if($pager['page_count'] <= $offset * 2)
	{
		for ($i=1; $i <= $pager['page_count']; $i++)
		{
			$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
		}
	}
	else
	{
		if($pager['page'] - $offset < 2)
		{
			$temp = $offset * 2;

			for ($i=1; $i<=$temp; $i++)
			{
				$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
			}

			$pager['page_nums'][] = array('name'=>'...');
			$pager['page_nums'][] = array('name' => $pager['page_count'],'url' => empty($url) ? str_replace('_page_',$pager['page_count'],$page_url) : $url . $pager['page_count']);
		}
		else
		{
			$pager['page_nums'][] = array('name' => 1,'url' => empty($url) ? str_replace('_page_',1,$page_url) : $url . 1);
			$pager['page_nums'][] = array('name'=>'...');
			$start = $pager['page'] - $offset + 1;
			$end = $pager['page'] + $offset - 1;

			if($pager['page_count'] - $end > 1)
			{
				for ($i=$start;$i<=$end;$i++)
				{
					$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
				}

				$pager['page_nums'][] = array('name'=>'...');
				$pager['page_nums'][] = array('name' => $pager['page_count'],'url' => empty($url) ? str_replace('_page_',$pager['page_count'],$page_url) : $url . $pager['page_count']);
			}
			else
			{
				$start = $pager['page_count'] - $offset * 2 + 1;
				$end = $pager['page_count'];
				for ($i=$start;$i<=$end;$i++)
				{
					$pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
				}
			}
		}
	}

	return $pager;
}

/**
 * 分页处理
 * @param int $total_count 总数
 * @param int $page 当前页
 * @param int $page_size 分页大小
 * @return array
 */
function buildPageMini($total_count,$page = 1,$page_size = 0)
{
	$pager['total_count'] = intval($total_count);
	$pager['page'] = $page;
	$pager['page_size'] = ($page_size == 0) ? ($_FANWE['setting']['page_listrows'] > 0 ? $_FANWE['setting']['page_listrows'] : 20) : $page_size;
	/* page 总数 */
	$pager['page_count'] = ($pager['total_count'] > 0) ? ceil($pager['total_count'] / $pager['page_size']) : 1;

	/* 边界处理 */
	if ($pager['page'] > $pager['page_count'])
		$pager['page'] = $pager['page_count'];

	$pager['limit'] = ($pager['page'] - 1) * $pager['page_size'] . "," . $pager['page_size'];

	$page_prev  = ($pager['page'] > 1) ? $pager['page'] - 1 : 1;
	$page_next  = ($pager['page'] < $pager['page_count']) ? $pager['page'] + 1 : $pager['page_count'];
	$pager['prev_page'] = $page_prev;
	$pager['next_page'] = $page_next;
	return $pager;
}

/**
 * 用于检测当前用户IP的可操作性,time_span为验证的时间间隔 秒
 *
 * @param string $ip_str  IP地址
 * @param string $module  操作的模块     *
 * @param integer $time_span 间隔
 * @param integer $id   操作的数据
 *
 * @return boolean
 */
function checkIpOperation($module,$time_span = 0,$id = 0)
{
	global $_FANWE;
	@session_start();
	$key = $_FANWE['client_ip'].'_'.$_FANWE['uid'].'_check_'.$module.($id > 0 ? '_'.$id : '');

	if(!isset($_SESSION[$key]))
	{
		$_SESSION[$key] = TIME_UTC;
		return true;
	}
	else
	{
		$time = (int)$_SESSION[$key];
		if(TIME_UTC - $time < $time_span)
		{
			return false;
		}
		else
		{
			$_SESSION[$key] = TIME_UTC;
			return true;
		}
	}
}

/**
 * 字符串截断处理
 * @param string $string 要处理的字符串
 * @param int  $length 指定长度
 * @param string $dot 超出指定长度时显示
 * @return array
 */
function cutStr($string, $length, $dot = '...')
{
	if(getStrLen($string) <= $length)
		return $string;

	$pre = '{%';
	$end = '%}';
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8')
	{
		$n = $tn = $noc = 0;
		while($n < strlen($string))
		{
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
			{
				$tn = 1; $n++; $noc++;
			}
			elseif(194 <= $t && $t <= 223)
			{
				$tn = 2; $n += 2; $noc += 2;
			}
			elseif(224 <= $t && $t <= 239)
			{
				$tn = 3; $n += 3; $noc += 2;
			}
			elseif(240 <= $t && $t <= 247)
			{
				$tn = 4; $n += 4; $noc += 2;
			}
			elseif(248 <= $t && $t <= 251)
			{
				$tn = 5; $n += 5; $noc += 2;
			}
			elseif($t == 252 || $t == 253)
			{
				$tn = 6; $n += 6; $noc += 2;
			}
			else
			{
				$n++;
			}

			if($noc >= $length)
				break;
		}

		if($noc > $length)
			$n -= $tn;

		$strcut = substr($string,0,$n);
	}
	else
	{
		for($i = 0; $i < $length; $i++)
		{
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return $strcut.$dot;
}

/**
 * 获取字符串长度
 * @param string $str 要获取长度的字符串
 * @return int
 */
function getStrLen($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

    if ($length)
    {
        return strlen($str) - $length + intval($length / 3) * 2;
    }
    else
    {
        return strlen($str);
    }
}

/**
 * 获取字节数
 * @param string $val 要获取字节数的字符串
 * @return int
 */
function getBytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last)
	{
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }

    return $val;
}

/**
 * 错误处理
 * @param string $message 错误信息
 * @param bool $show 是否显示
 * @param bool $save 是否保存
 * @param bool $halt 是否停止
 * @return void
 */
function systemError($message, $show = true, $save = true, $halt = true)
{
	require_once fimport('class/error');
	FanweError::systemError($message, $show, $save, $halt);
}

/**
 * 显示成功信息
 * @param string $title 标题
 * @param string $message 成功信息
 * @param string $jump_url 跳转地址
 * @param int $wait 等待时间
 * @return void
 */
function showSuccess($title, $message,$jump_url,$wait = 3)
{
	global $_FANWE;
	include template('page/success');
	display();
	exit;
}

/**
 * 显示错误信息
 * @param string $title 标题
 * @param string $message 错误信息
 * @param string $jump_url 跳转地址
 * @param int $wait 等待时间
 * @param bool $is_close 是否显示网站关闭
 * @return void
 */
function showError($title, $message,$jump_url,$wait = 3,$is_close = false)
{
	global $_FANWE;

	if($is_close)
		include template('page/close');
	else
		include template('page/error');

	display();
	exit;
}

/**
 * 查询字符串是否存在
 * @param string $string
 * @param string $find
 * @return bool
 */
function strExists($string, $find)
{
	return !(strpos($string, $find) === FALSE);
}

/**
 * 获取是否为搜索引擎爬虫
 * @param string $userAgent 用户信息
 * @return bool
 */
function checkRobot($userAgent = '')
{
	static $kwSpiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
	static $kwBrowsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';

	$userAgent = empty($userAgent) ? $_SERVER['HTTP_USER_AGENT'] : $userAgent;

	if(!strExists($userAgent, 'http://') && preg_match("/($kwBrowsers)/i", $userAgent))
		return false;
	elseif(preg_match("/($kwSpiders)/i", $userAgent))
		return true;
	else
		return false;
}

/**
 * 获取链接格式是否正确
 * @param string $url 链接
 * @return bool
 */
function parseUrl($url)
{
	$parse_url = parse_url($url);
	return (!empty($parse_url['scheme']) && !empty($parse_url['host']));
}


/**
 * 获取客户端IP
 * @return string
 */
function getFClientIp()
{
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
	{
		foreach ($matches[0] AS $xip)
		{
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip))
			{
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}

/**
 * 字符转义
 * @return string
 */
function fAddslashes($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
		{
			unset($string[$key]);
			$string[addslashes($key)] = fAddslashes($val);
		}
	}
	else
	{
		$string = addslashes($string);
	}

	return $string;
}

/**
 * 字符转义
 * @return string
 */
function fStripslashes($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
		{
			unset($string[$key]);
			$string[stripslashes($key)] = fStripslashes($val);
		}
	}
	else
	{
		$string = stripslashes($string);
	}

	return $string;
}

/**
 * 生成随机数
 * @param int $length 随机数长度
 * @param int $numeric 是否只生成数字
 * @return string
 */
function random($length, $numeric = 0)
{
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++)
	{
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

/**
 * 生成cookie
 * @param string $var 键名
 * @param string $value 值
 * @param int $life 过期时间
 * @param bool $prefix 是否加入前缘
 * @param bool $http_only
 * @return void
 */
function fSetCookie($var, $value = '', $life = 0, $prefix = true, $http_only = false)
{
	global $_FANWE;
	$config = $_FANWE['config']['cookie'];
	$_FANWE['cookie'][$var] = $value;
	$var = ($prefix ? $config['cookie_pre'] : '').$var;
	$_COOKIE[$var] = $value;

	if($value == '' || $life < 0)
	{
		$value = '';
		$life = -1;
	}

	$life = $life > 0 ? TIME_UTC + $life : ($life < 0 ? TIME_UTC - 31536000 : 0);
	$path = $http_only && PHP_VERSION < '5.2.0' ? $config['cookie_path'].'; HttpOnly' : $config['cookie_path'];

	$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
	if(PHP_VERSION < '5.2.0')
	{
		setcookie($var, $value, $life, $path, $config['cookie_domain'], $secure);
	}
	else
	{
		setcookie($var, $value, $life, $path, $config['cookie_domain'], $secure, $http_only);
	}
}

/**
 * 更新session
 * @param bool $force 强制更新
 * @return bool
 */
function updateSession($force = false) {

	global $_FANWE;
	static $updated = false;

	if(!$updated)
	{
		$fanwe = & FanweService::instance();
		foreach($fanwe->session->var as $k => $v)
		{
			if(isset($_FANWE['user'][$k]) && $k != 'last_activity')
				$fanwe->session->set($k, $_FANWE['user'][$k]);
		}

		$fanwe->session->update();

		$updated = true;
	}

	return $updated;
}

/**
 * 获取cookie
 * @param string $key 键名
 * @return bool
 */
function getCookie($key)
{
	global $_FANWE;
	return isset($_FANWE['cookie'][$key]) ? $_FANWE['cookie'][$key] : '';
}

/**
 * 生成表单随机数
 * @param string $specialadd 增加文本
 * @return string
 */
function formHash($specialadd = '')
{
	global $_FANWE;
	return substr(md5(substr(TIME_UTC, 0, -7).$_FANWE['user_name'].$_FANWE['uid'].$_FANWE['authkey'].$specialadd), 8, 8);
}

/**
 * 安全代码处理
 * @param string $string 要处理的文本
 * @param string $operation 处理方式(DECODE:解码,ENCODE:编码)
 * @param string $key 密匙
 * @param int $expiry 过期时间
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	global $_FANWE;
	$ckey_length = 4;
	$key = md5($key != '' ? $key : $_FANWE['authkey']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE')
	{
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
		{
			return substr($result, 26);
		} else {
			return '';
		}
	}
	else
	{
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

/**
 * 获取语言文本
 * @param string $file 所在文件
 * @param string $var 键
 * @param string $default 默认值
 * @return mixed
 */
function lang($file, $var = NULL, $default = NULL)
{
	global $_FANWE;

	$key = $file."_lang";

	if(!isset($_FANWE['lang'][$key]))
	{
		include fimport("language/$file");
		$_FANWE['lang'][$key] = $lang;
	}

	$return = $var !== NULL ? (isset($_FANWE['lang'][$key][$var]) ? $_FANWE['lang'][$key][$var] : NULL) : $_FANWE['lang'][$key];

	$return = $return === NULL ? ($default !== NULL ? $default : $var) : $return;

	return $return;
}

/**
 * 获取IP列表中是否存在指定的IP
 * @param string $ip ip
 * @param string $access_list ip列表
 * @return bool
 */
function ipAccess($ip, $access_list)
{
	return preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($access_list, '/')).")/", $ip);
}

/**
 * 获取IP是否充许访问
 * @param string $ip ip
 * @return bool
 */
function ipBanned($ip)
{
	global $_FANWE;

	if($_FANWE['setting']['ip_access'] && !ipAccess($ip, $_FANWE['setting']['ip_access']))
	{
		return true;
	}

	FanweService::instance()->cache->loadCache('ipbanned');

	if(empty($_FANWE['cache']['ipbanned']))
		return false;
	else
	{
		if($_FANWE['cache']['ipbanned']['expiration'] < TIME_UTC)
		{
			FanweService::instance()->cache->updateCache('ipbanned');
		}

		return preg_match("/^(".$_FANWE['cache']['ipbanned']['regexp'].")$/", $ip);
	}
}

/**
 * 获取模板cache文件路径
 * @param string $file 模板文件
 * @param array $args 参数
 * @param int $is_dynamic 是否为动态缓存(动态缓存页面在清空缓存，不删除)
 * @param string $dir 缓存目录
 * @return string
 */
function getTplCache($file, $args, $is_dynamic = 0,$dir = '')
{
	global $_FANWE;
	$tpl_dir = './tpl/'.$_FANWE['setting']['site_tmpl'];
	$tpl_file = $tpl_dir.'/'.$file.'.htm';

	if(!empty($dir))
		$dir .= '/';
	
	switch($is_dynamic)
	{
		case 1:
			$dir = 'dynamic/'.$dir;
		break;
		
		case 2:
			$dir = 'page/'.$dir;
		break;
		
		default:
			$dir = 'static/'.$dir;
		break;
	}
	$filename = md5($tpl_file.implode(',',$args));
	return PUBLIC_ROOT.'./data/tpl/caches/'.$dir.str_replace('/', '_', $file).'/'.substr($filename,0,1)."/".substr($filename,1,1)."/".$filename.".htm";
}

/**
 * 检测模板是否需要更新
 * @param string $main_tpl
 * @param string $sub_tpl
 * @param int $time_compare
 * @param string $cache_file
 * @param string $tpl_dir
 * @param string $file
 * @return bool
 */
function checkTplRefresh($main_tpl, $sub_tpl, $time_compare, $cache_file, $tpl_dir, $file)
{
	global $_FANWE;
	static $tpl_refresh = NULL;
	if($tpl_refresh === NULL)
	{
		$tpl_refresh = $_FANWE['config']['output']['tpl_refresh'];
	}

	if(empty($time_compare) || $tpl_refresh == 1 || ($tpl_refresh > 1 && !(TIMESTAMP % $tpl_refresh)))
	{
		if(empty($time_compare) || @filemtime(FANWE_ROOT.$sub_tpl) > $time_compare)
		{
			require_once fimport('class/template');
			$template = new Template();
			$template->parseTemplate($main_tpl, $tpl_dir, $file, $cache_file);
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * 模板处理
 * @param string $file
 * @param string $tpl_dir
 * @param bool $get_tpl_file
 * @return string
 */
function template($file, $tpl_dir = '', $get_tpl_file = 0)
{
	global $_FANWE;

	$tpl_dir = $tpl_dir ? $tpl_dir : './tpl/'.$_FANWE['setting']['site_tmpl'];
	$tpl_file = $tpl_dir.'/'.$file.'.htm';
	$cache_file = './data/tpl/compiled/'.str_replace('/', '_', $file).'.tpl.php';

	if($get_tpl_file)
		return $tpl_file;

	checkTplRefresh($tpl_file, $tpl_file, @filemtime(PUBLIC_ROOT.$cache_file), $cache_file, $tpl_dir, $file);
	return PUBLIC_ROOT.$cache_file;
}

/**
 * 获取模板编译后内容
 * @param string $file
 * @param array $args
 * @param string $tpl_dir
 * @return string
 */
function tplFetch($file,&$args = array(), $tpl_dir = '',$cache_file = '')
{
	global $_FANWE;

	if(!empty($args))
	{
		foreach($args as $key=>$val)
		{
			$$key = &$args[$key];
		}
	}

	ob_start();
	if(!empty($cache_file) && file_exists($cache_file))
		include $cache_file;
	else
		include template($file,$tpl_dir);
	$content = ob_get_contents();
	ob_end_clean();
	
	express($content);
	if(!empty($cache_file) && !file_exists($cache_file) && diskfreespace(PUBLIC_ROOT.'./data/tpl/caches') > 1000000)
	{
		if(makeDir(preg_replace("/^(.*)\/.*?\.htm$/is", "\\1", $cache_file)))
			writeFile($cache_file,$content);
	}

	require_once fimport('dynamic/common');
	$module_dynamic = '';
	if(defined('MODULE_NAME') && MODULE_NAME != '')
		$module_dynamic = fimport('dynamic/'.MODULE_NAME);

	if(!empty($module_dynamic) && file_exists($module_dynamic))
		require_once $module_dynamic;

	$content = preg_replace('/<!--dynamic\s+(.+?)(?:|\sargs=(.*?))-->/ies', "\\1('\\2');", $content);
	
	//格式化会员关注
	formatUserFollowTags($content);
	contentParse($content);
	
	return $content;
}

/**
 * 处理模板字符串，并返回编译后内容
 * @param string $string
 * @param string $cache_key
 * @param array $args
 * @return string
 */
function tplString($string,$cache_key,&$args = array())
{
	global $_FANWE;

	if(!empty($args))
	{
		foreach($args as $key=>$val)
		{
			$$key = &$args[$key];
		}
	}

	if(empty($cache_key))
		$cache_key = md5($string);

	$cache_file = PUBLIC_ROOT.'./data/tpl/caches/'.$cache_key.'.htm';

	if(!file_exists($cache_file))
	{
		if(makeDir(preg_replace("/^(.*)\/.*?\.htm$/is", "\\1", $cache_file)))
		{
			require_once fimport('class/template');
			$template = new Template();
			$string = $template->parseString($string);
			writeFile($cache_file,$string);
		}
	}

	ob_start();
	include $cache_file;
	$content = ob_get_contents();
	ob_end_clean();

	require_once fimport('dynamic/common');
	$module_dynamic = '';
	if(defined('MODULE_NAME') && MODULE_NAME != '')
		$module_dynamic = fimport('dynamic/'.MODULE_NAME);

	if(!empty($module_dynamic) && file_exists($module_dynamic))
		require_once $module_dynamic;
	
	$content = preg_replace('/<!--dynamic\s+(.+?)(?:|\sargs=(.*?))-->/ies', "\\1('\\2');", $content);
	//格式化会员关注
	express($content);
	formatUserFollowTags($content);
	contentParse($content);
	return $content;
}

/**
 * 显示页面
 * @param string $cache_file 缓存路径
 * @param bool $is_session 是否更新session
 * @param bool $is_return 是否返回页面内容
 * @return mixed
 */
function display($cache_file,$is_session = true,$is_return = false)
{
	global $_FANWE;
	$content = NULL;
	if(!empty($cache_file) && !file_exists($cache_file) && diskfreespace(PUBLIC_ROOT.'./data/tpl/caches') > 1000000)
	{
		if(makeDir(preg_replace("/^(.*)\/.*?\.htm$/is", "\\1", $cache_file)))
		{
			$css_script_php = '';
			if(isset($_FANWE['page_parses']))
				$css_script_php = "<?php\n".'$_FANWE[\'CACHE_CSS_SCRIPT_PHP\']'." = ".var_export($_FANWE['page_parses'], true).";\n?>";
			
			$content = ob_get_contents();
			express($content);
			writeFile($cache_file,$css_script_php.$content);
		}
	}

	require_once fimport('dynamic/common');
	$module_dynamic = '';
	if(defined('MODULE_NAME') && MODULE_NAME != '')
		$module_dynamic = fimport('dynamic/'.MODULE_NAME);

	if(!empty($module_dynamic) && file_exists($module_dynamic))
		require_once $module_dynamic;

	if($content === NULL)
	{
		$content = ob_get_contents();
		express($content);
	}
	ob_end_clean();
	$content = preg_replace('/<!--dynamic\s+(.+?)(?:|\sargs=(.*?))-->/ies', "\\1('\\2');", $content);
	
	if(isset($_FANWE['CACHE_CSS_SCRIPT_PHP']))
	{
		if(isset($_FANWE['CACHE_CSS_SCRIPT_PHP']['css']))
		{
			foreach($_FANWE['CACHE_CSS_SCRIPT_PHP']['css'] as $url)
			{
				cssParse($url);
			}
		}
		
		if(isset($_FANWE['CACHE_CSS_SCRIPT_PHP']['script']))
		{
			foreach($_FANWE['CACHE_CSS_SCRIPT_PHP']['script'] as $url)
			{
				scriptParse($url);
			}
		}
	}
	
	//格式化会员关注
	formatUserFollowTags($content);
	contentParse($content);
	
	if($is_session)
		updateSession();

	if($is_return)
		return $content;

	$_FANWE['gzip_compress'] ? ob_start('ob_gzhandler') : ob_start();

	echo $content;
}

function contentParse(&$content)
{
	global $_FANWE;

	$patterns = array (
		"/\.\/public\/js\//i",
		"/\.\/public\/upload\//i",
		"/\.\/public\//i",
		//"/\.\/tpl\/(.*?)\/css\//i",
		//"/\.\/tpl\/(.*?)\/js\//i",
		//"/\.\/tpl\/(.*?)\/images\//i",
		"/\.\/tpl\/css\//i",
		"/\.\/tpl\/images\//i",
		"/\.\/tpl\/js\//i",
		"/\.\/tpl\/(.*?)\//i",
	);

	$image_url = !empty($_FANWE['config']['cdn']['image']) ? $_FANWE['config']['cdn']['image'] : $_FANWE['site_root'];
	$css_url = !empty($_FANWE['config']['cdn']['css']) ? $_FANWE['config']['cdn']['css'] : $_FANWE['site_root'];
	$js_url = !empty($_FANWE['config']['cdn']['js']) ? $_FANWE['config']['cdn']['js'] : $_FANWE['site_root'];

	$replace = array (
		$js_url.'public/js/',
		$image_url.'public/upload/',
		$_FANWE['site_root'].'public/',
		//$css_url.'tpl/\\1/css/',
		//$js_url.'tpl/\\1/js/',
		//$image_url.'tpl/\\1/images/',
		$_FANWE['site_root'].'public/data/tpl/css/',
		$_FANWE['site_root'].'tpl/'.TMPL.'/images/',
		$_FANWE['site_root'].'tpl/'.TMPL.'/js/',
		$_FANWE['site_root'].'tpl/\\1/',
	);

	if(FS('Image')->getIsServer())
	{
		if(!isset($_FANWE['cache']['image_servers']))
			FanweService::instance()->cache->loadCache('image_servers');
		foreach($_FANWE['cache']['image_servers']['all'] as $server)
		{
			$patterns[] = "/\.\/".$server['code']."\//i";
			$replace[] = $server['url'];
		}
	}

	$content = preg_replace($patterns,$replace, $content);
}

function clearExpress($content)
{
	return preg_replace("/\[[^\]]+\]/i",'',$content);
}

function express(&$content)
{
	global $_FANWE;

	$express = getCache('emotion_express_cache'); //缓存过的表情hash
	if(!$express)
	{
		$express_rs = FDB::fetchAll("select `emotion`,concat('./public/expression/',`type`,'/',`filename`) as fname from ".FDB::table('expression'));
		foreach($express_rs as $k=>$row)
		{
			$express[0][] = $row['emotion'];
			$express[1][] = "<img src='".$row['fname']."' title='".preg_replace("/[\[\]]/",'',$row['emotion'])."' />";
		}
		setCache('emotion_express_cache',$express);
	}
	
	preg_match("/(<html.*?>.*?<\/head>)/s",$content,$data);
	$head_html = $data[1];
	$content = str_replace($head_html,'<!--TAG_HEADER-->',$content);

	preg_match_all("/(<textarea.*?>.*?<\/textarea>)/s",$content,$data);
	$textareas = $data[1];
	if(count($textareas) > 0)
	{
		foreach($textareas as $key => $textarea)
		{
			$content = str_replace($textarea,'<!--TAG_TEXTAREA_'.$key.'-->',$content);
		}
	}

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
			$patterns[] = '/@'.preg_quote($data['user_name']).'(\:| )/';
			$replace[] = '<a class="u_name GUID" uid="'.$data['uid'].'" href="'.FU('u/index',array('uid'=>$data['uid'])).'">@'.$data['user_name']."</a>\$1";
		}

		$content = preg_replace($patterns,$replace,$content);
	}
	
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
			$patterns[] = '#'.$data['title'].'#';
			$replace[] = '<a href="'.FU("event/detail",array("id"=>$data['id'])).'" target="_blank">#'.$data['title'].'#</a>';
		}

		$content = str_replace($patterns,$replace,$content);
	}
	
	if(count($_FANWE['tpl_user_formats']) > 0)
	{
		$patterns = array();
		$replace = array();
		
		$user_ids = array_keys($_FANWE['tpl_user_formats']);
		$user_ids = implode(',',$user_ids);
		$user_ids = str_replace(',,',',',$user_ids);
		if(!empty($user_ids))
		{
			$res = FDB::query("SELECT uid,user_name,server_code,reg_time,credits,is_daren,is_buyer,follows,fans,collects,
				favs,threads,photos,goods,ask,ask_posts,ask_best_posts,shares,forums,forum_posts,
				seconds,albums,referrals FROM ".FDB::table('user').' 
				INNER JOIN '.FDB::table('user_count').' AS uc USING(uid) 
				WHERE uid IN ('.$user_ids.')');
			while($user = FDB::fetch($res))
			{
				$uid = $user['uid'];
				$user['url'] = FU('u/index',array('uid'=>$uid));
				foreach($_FANWE['tpl_user_formats'][$uid] as $tuf_key => $tuf_val)
				{
					$patterns[] = "<!--USER_".$uid."_".$tuf_key."-->";
					$replace[] = getUserFormatHtml($user,$tuf_val);
				}
				unset($_FANWE['tpl_user_formats'][$uid]);
			}
			$content = str_replace($patterns,$replace,$content);
		}
	}
	
	$content = str_replace($express[0],$express[1],$content);
	$content = str_replace('<!--TAG_HEADER-->',$head_html,$content);

	if(count($textareas) > 0)
	{
		foreach($textareas as $key => $textarea)
		{
			$content = str_replace('<!--TAG_TEXTAREA_'.$key.'-->',$textarea,$content);
		}
	}
	return $content;
}

//加入会员格式化
function setTplUserFormat($uid,$type,$is_mark,$img_type,$img_size,$link_class,$img_class,$tpl)
{
	global $_FANWE;
	$uid = (int)$uid;
	$key = md5($type.'_'.$is_mark.'_'.$img_type.'_'.$img_size.'_'.$link_class.'_'.$img_class.'_'.$tpl);
	$_FANWE['tpl_user_formats'][$uid][$key] = array(
		'type' => $type,
		'is_mark'=>$is_mark,
		'img_type' => $img_type,
		'img_size' => $img_size,
		'link_class' => $link_class,
		'img_class' => $img_class,
		'tpl' => $tpl
	);
	return "<!--USER_".$uid."_".$key."-->";
}

//获取会员格式化html
function getUserFormatHtml($user,$format)
{
	global $_FANWE;
	static $templates = array(),$daren_name = NULL;
	$html = '';
	if(!empty($format['tpl']))
	{
		if(!isset($templates[$format['tpl']]))
			$templates[$format['tpl']] = template($format['tpl']);
		
		if($templates[$format['tpl']])
		{
			ob_start();
			include $templates[$format['tpl']];
			$html = ob_get_contents();
			ob_end_clean();
		}
	}
	else
	{
		$uid = $user['uid'];
		$user_name = htmlspecialchars($user['user_name']);
		if($format['type'] == 0)
			$html = '<a class="GUID '.$format['link_class'].'" uid="'.$uid.'" title="'.$user_name.'" href="'.$user['url'].'" target="_blank">'.$user_name.'</a>';
		else
		{
			$width = '';
			if($format['img_size'] > 0)
				$width = 'width="'.$format['img_size'].'" ';
			
			$is_lazyload = FALSE;
			$img_class = $format['img_class'];
			if(!empty($img_class))
				$is_lazyload = strpos($img_class,'lazyload');
			
			$link_class = '';
			if(!empty($format['link_class']))
				$link_class = 'class="'.$format['link_class'].'" ';
			
			if($is_lazyload === FALSE)
				$html = '<a '.$link_class.'title="'.$user_name.'" href="'.$user['url'].'" target="_blank"><img class="GUID '.$img_class.'" uid="'.$uid.'" src="'.avatar($uid,$format['img_type'],$user['server_code'],1).'" '.$width.' alt="'.$user_name.'"/></a>';
			else
				$html = '<a '.$link_class.'title="'.$user_name.'" href="'.$user['url'].'" target="_blank"><img class="GUID '.$img_class.'" uid="'.$uid.'" original="'.avatar($uid,$format['img_type'],$user['server_code'],1).'" src="./tpl/images/lazyload.gif" '.$width.' alt="'.$user_name.'"/></a>';
		}
		
		if($format['is_mark'] == 1)
		{
			if($user['is_daren'] == 1)
			{
				if($daren_name === NULL)
					$daren_name = sprintf(lang('user','daren_alt'),$_FANWE['setting']['site_name']);
				$html .= '<a href="'.FU('daren/apply').'" class="v" target="_blank"><img title="'.$daren_name.'" src="./tpl/images/daren_icon.png" class="v"></a>';
			}
			elseif($user['is_buyer'] == 1)
				$html .= '<a href="'.FU('settings/buyerverifier').'" class="v" target="_blank"><img title="'.lang('user','buyer_alt').'" src="./tpl/images/buyer_icon.png" class="v"></a>';
		}
	}
	return $html;
}

function formatUserFollowTags(&$content)
{
	global $_FANWE;
	
	preg_match_all("/<!--getfollow\s(\d+?)\s(.+?)-->/",$content,$follows);
	if(!empty($follows[1]))
	{
		$patterns = array();
		$replace = array();
		$user_ids = array();
		
		foreach($follows[1] as $key => $uid)
		{
			$uid = (int)$uid;
			$tpl = $follows[2][$key];
			if($_FANWE['uid'] == $uid)
			{
				if(!isset($user_ids[$uid][$tpl]))
				{
					$patterns[] = "<!--getfollow ".$uid." ".$tpl."-->";
					$replace[] = getUserFollowFormatHtml($uid,-1,$tpl);
					$user_ids[$uid][$tpl] = -1;
				}
			}
			else
			{
				$user_ids[$uid]['is_follow'] = 0;
				$user_ids[$uid]['tpls'][$tpl] = 0;
			}
		}
		unset($user_ids[$_FANWE['uid']]);

		if($_FANWE['uid'] > 0)
		{
			$follow_ids = array_keys($user_ids);
			if(count($follow_ids) > 0)
			{
				$follow_ids = implode(',',$follow_ids);
				$follow_ids = str_replace(',,',',',$follow_ids);
				if(!empty($follow_ids))
				{
					$res = FDB::query("SELECT uid FROM ".FDB::table('user_follow').' 
						WHERE f_uid = '.$_FANWE['uid'].' AND uid IN ('.$follow_ids.')');
					while($item = FDB::fetch($res))
					{
						$user_ids[$item['uid']]['is_follow'] = 1;
					}
				}
			}
		}
		
		foreach($user_ids as $uid => $user)
		{
			$is_follow = $user['is_follow'];
			foreach($user['tpls'] as $tpl => $temp)
			{
				$patterns[] = "<!--getfollow ".$uid." ".$tpl."-->";
				$replace[] = getUserFollowFormatHtml($uid,$is_follow,$tpl);
			}
		}
		
		$content = str_replace($patterns,$replace,$content);
	}
}

//获取会员关注格式化html
function getUserFollowFormatHtml($uid,$is_follow,$tpl)
{
	static $templates = array();
	$html = '';
	
	if(!isset($templates[$tpl]))
		$templates[$tpl] = template($tpl);
	
	if($templates[$tpl])
	{
		ob_start();
		include $templates[$tpl];
		$html = ob_get_contents();
		ob_end_clean();
	}
	
	return $html;
}

/**
 * 清除缓存目录
 * @param string $file 缓存模板目录
 * @param int $is_dynamic 是否为动态缓存
 * @param string $dir 缓存目录
 * @return void
 */
function clearTplCache($file,$is_dynamic = 0,$dir='')
{
	if(!empty($dir))
		$dir .= '/';
	$dir = ($is_dynamic == 1 ? 'dynamic/' : 'static/').$dir;
	clearDir(PUBLIC_ROOT.'./data/tpl/caches/'.$dir.str_replace('/', '_', $file));
}

/**
 * 检测缓存文件是否需要更新
 * @param string $cache_file 缓存文件路径
 * @param int $time_out 缓存时间(秒)
 * @param int $is_clear 清除缓存
 * @return bool 需要更新返回 true
 */
function getCacheIsUpdate($cache_file,$time_out,$is_clear = 0)
{
	if (!file_exists($cache_file))
		return true;
	
	$time_clear = 0;
	if($is_clear == 1)
	{
		$clear_path = FANWE_ROOT.'./public/data/tpl/caches/page/is_clear.lock';
		if(file_exists($clear_path))
			$time_clear = (int)@file_get_contents($clear_path);
	}

	$mtime = filemtime($cache_file);
	if($time_clear > 0 && $mtime < $time_clear)
	{
		removeFile($cache_file);
		return true;
	}
	elseif(TIMESTAMP - $mtime > $time_out)
	{
		removeFile($cache_file);
		return true;
	}
	else
		return false;
}

/**
 * 输出json信息
 * @param mixed $result 要输出的信息
 * @return void
 */
function outputJson($result,$is_die = true)
{
	static $json = NULL;
	if($json === NULL)
	{
		require fimport('class/json');
		$json = new Json();
	}
	
	if($is_die)
		die($json->encode($result));
	else
		return $json->encode($result);
}

/**
 * 返回json信息
 * @param mixed $result
 * @return string
 */
function getJson($result)
{
	static $json = NULL;
	if($json === NULL)
	{
		require fimport('class/json');
		$json = new Json();
	}
	return $json->encode($result);
}

/**
 * 清除指定目录下的文件
 * @param string $dir 目录路径
 * @return void
 */
function clearDir($dir,$is_del_dir = false)
{
	if(!file_exists($dir))
		return;

	$directory = dir($dir);

	while($entry = $directory->read())
	{
		if($entry != '.' && $entry != '..')
		{
			$filename = $dir.'/'.$entry;
			if(is_dir($filename))
				clearDir($filename,$is_del_dir);

			if(is_file($filename))
				removeFile($filename);
		}
	}

	$directory->close();
	if($is_del_dir)
		@rmdir($dir);
}

/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function makeDir($folder)
{
    $reval = false;
    if (!file_exists($folder))
    {
		$folder = str_replace(FANWE_ROOT,'',$folder);
        /* 如果目录不存在则尝试创建该目录 */
        @umask(0);
        /* 将目录路径拆分成数组 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
        /* 如果第一个字符为/则当作物理路径处理 */
        $base = FANWE_ROOT.(($atmp[0][0] == '/') ? '/' : '');

        /* 遍历包含路径信息的数组 */
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;
                if ('..' == $val || '.' == $val)
                {
                    /* 如果目录为.或者..则直接补/继续下一个循环 */
                    $base .= '/';
                    continue;
                }
            }
            else
            {
                continue;
            }
            $base .= '/';

            if (!file_exists($base))
            {
                /* 尝试创建目录，如果创建失败则继续循环 */
                if (@mkdir(rtrim($base, '/'), 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        /* 路径已经存在。返回该路径是不是一个目录 */
        $reval = is_dir($folder);
    }

    clearstatcache();
    return $reval;
}

/**
 * utf8字符串转为GBK字符串
 * @param string $str 要转换的字符串
 * @return void
 */
function utf8ToGB($str)
{
	static $chinese = NULL;
	if($chinese === NULL)
	{
		require_once fimport('class/chinese');
		$chinese = new Chinese('UTF-8','GBK');
	}
	return $chinese->convert($str);
}

/**
 * GBK字符串转utf8为字符串
 * @param string $str 要转换的字符串
 * @return void
 */
function gbToUTF8($str)
{
	static $chinese = NULL;
	if($chinese === NULL)
	{
		require_once fimport('class/chinese');
		$chinese = new Chinese('GBK','UTF-8');
	}
	return $chinese->convert($str);
}

/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8ToUnicode($char)
{
	switch(strlen($char))
	{
		case 1:
			return ord($char);
		case 2:
			$n = (ord($char[0]) & 0x3f) << 6;
			$n += ord($char[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($char[0]) & 0x1f) << 12;
			$n += (ord($char[1]) & 0x3f) << 6;
			$n += ord($char[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($char[0]) & 0x0f) << 18;
			$n += (ord($char[1]) & 0x3f) << 12;
			$n += (ord($char[2]) & 0x3f) << 6;
			$n += ord($char[3]) & 0x3f;
			return $n;
	}
}

/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @param string $pre
 * @return string
 */
function segmentToUnicode($str,$pre = '')
{
	$arr = array();
	$str_len = mb_strlen($str,'UTF-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'UTF-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = $pre.'ux'.utf8ToUnicode($s);
		}
	}

	$arr = array_unique($arr);

	return implode(' ',$arr);
}

/**
 * 将标签数组转换为unicode字符串
 * @param array $tags 要转换的标签
 * @param string $pre
 * @return string
 */
function tagToUnicode($tags,$pre = '')
{
	$tags = array_unique($tags);

	$arr = array();
	foreach($tags as $tag)
	{
		$tmp = '';
		$str_len = mb_strlen($tag,'UTF-8');
		for($i = 0;$i < $str_len;$i++)
		{
			$s = mb_substr($tag,$i,1,'UTF-8');
			if($s != ' ' && $s != '　')
			{
				$tmp.= 'ux'.utf8ToUnicode($s);
			}
		}

		if($tmp != '')
			$arr[] = $pre.$tmp;
	}

	$arr = array_unique($arr);
	return implode(' ',$arr);
}

/**
 * 清除符号
 * @param string $str 要清除符号的字符串
 * @return string
 */
function clearSymbol($str)
{
	static $symbols = NULL;
	if($symbols === NULL)
	{
		$symbols = file_get_contents(PUBLIC_ROOT.'./table/symbol.table');
		$symbols = explode("\r\n",$symbols);
	}

	return str_replace($symbols,"",$str);
}

/**
 * 对 MYSQL LIKE 的内容进行转义
 *
 * @access      public
 * @param       string      string  内容
 * @return      string
 */
function fMysqlLikeQuote($str)
{
    return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
}

/**
 * 页面跳转
 * @param string $string
 * @param bool $replace
 * @param int $http_response_code
 * @return void
 */
function fHeader($string, $replace = true, $http_response_code = 0)
{
	global $_FANWE;
	
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	$reg = '/location:\s?'.preg_quote($_FANWE['site_root'], '/').'/i';
	$string = preg_replace($reg,'Location: '.$_FANWE['site_url'], $string);
	
	if(strpos($string,$_FANWE['site_url']))
	{
		fSetCookie('from_heade',authcode(TIME_UTC,'ENCODE'));
	}
	
	if(empty($http_response_code) || PHP_VERSION < '4.3' )
	{
		@header($string, $replace);
	}
	else
	{
		@header($string, $replace, $http_response_code);
	}

	if(preg_match('/^\s*location:/is', $string))
	{
		exit();
	}
}

/**
 * 显示指定名称的广告位布局
 * @param string $id 布局编号
 * @param int $count 显示数量
 * @param string $target 关键字
 * @return array
 */
function getAdvLayout($id,$count = '',$target='')
{
	global $_FANWE;
	$layout = FDB::fetchFirst('SELECT rec_id AS pid,item_limit AS acount,target_id AS target FROM '.FDB::table('layout')." WHERE layout_id ='$id' AND tmpl = '".$_FANWE['setting']['site_tmpl']."' AND rec_module = 'AdvPosition'");

	if(!$layout)
		return '';

	if($count != '')
		$layout['acount'] = intval($count);

	if($target != '')
		$layout['target'] = explode(',',$target);

	return getAdvPosition($layout['pid'],$layout['acount'],$layout['target']);
}

/**
 * 显示指定ID的广告位ID
 * @param string $id 广告位
 * @param int $count 显示数量
 * @param string $target 关键字
 * @return array
 */
function getAdvPosition($id,$count = '',$target='')
{
	global $_FANWE;

	$ap = FDB::fetchFirst('SELECT * FROM '.FDB::table('adv_position').' WHERE id ='.$id);
	if(!$ap)
		return '';

	if($target != '')
		$target = explode(',',$target);

	$where = "status = 1 AND position_id = '$id'";
	if($target != '')
		$where .= ' AND target_key'.FDB::createIN($target);

	$sql = 'SELECT * FROM '.FDB::table('adv').' WHERE '.$where.' ORDER BY sort ASC,id DESC';

	if($count > 0)
		$sql .= ' LIMIT 0,'.$count;

	$adv_res = FDB::query($sql);

	$adv_list = array();

	while($adv = FDB::fetch($adv_res))
	{
		$adv['html'] = getAdvHTML($adv,$ap);
		$adv['durl'] = FU("adv/show",array("id"=>$adv['id']));
		$adv['turl'] = $adv['url'];
		$adv['url'] = urlencode(FU("adv/show",array("id"=>$adv['id'])));
		$adv_list[] = $adv;
	}

	$ap['adv_list'] = $adv_list;
	return $ap;
}

/**
 * 获取广告的html代码
 * @param array $adv 广告
 * @param array $ap 广告位
 * @return string
 */
function getAdvHTML($adv,$ap)
{
	if($ap['width'] == 0)
		$ap['width']="";
	else
		$ap['width']=" width='".$ap['width']."'";

	if($ap['height'] == 0)
		$ap['height']="";
	else
		$ap['height']=" height='".$ap['height']."'";

	switch($adv['type'])
	{
		case '1':
			if($adv['url']=='')
				$adv_str = "<img src='".$adv['code']."'".$ap['width'].$ap['height']."/>";
			else
				$adv_str = "<a href='".FU("adv/show",array("id"=>$adv['id']))."' target='_blank'><img src='".$adv['code']."'".$ap['width'].$ap['height']."/></a>";
			break;
		case '2':
			$adv_str = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0'".$ap['width'].$ap['height'].">".
					   "<param name='movie' value='".$adv['code']."' />".
    				   "<param name='quality' value='high' />".
					   "<param name='menu' value='false' />".
					   "<embed src='".$adv['code']."' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'".$ap['width'].$ap['height']."></embed>".
					   "</object>";
			break;
		case '3':
			$adv_str = $adv['code'];
			break;
	}

	return $adv_str;
}

/**
 * 写入文件内容
 * @param string $filepat 文件路径
 * @param string $content 写入内容
 * @param string $type 写入方式 w:将文件指针指向文件头并将文件大小截为零 a:将文件指针指向文件末尾
 * @return string
 */
function writeFile($filepath,$content,$type='w')
{
	$is_success = false;

	if($fp = fopen($filepath,$type))
	{
		/*$start_time = microtime();
		do
		{
	        $is_write = flock($fp, LOCK_EX);
			if(!$is_write)
				usleep(round(rand(0,100) * 1000));
		}
		while(!$is_write && ((microtime() - $start_time) < 10000));

		if ($is_write && fwrite($fp, $content))
	  		$is_success = true;*/
		@flock($fp, LOCK_EX);
		if (fwrite($fp, $content))
	  		$is_success = true;
		@flock($fp,LOCK_UN);
		@fclose($fp);
		@chmod($filepath, 0777);
	}

	return $is_success;
}

/**
 * 删除文件
 * @param string $filepat 文件路径
 * @return bool
 */
function removeFile($filepath)
{
	$is_success = false;
	/*do
	{
		@unlink($filepath);
		$is_exists = file_exists($filepath);
		if($is_exists)
			usleep(round(rand(0,100) * 1000));
		else
			$is_success = true;
	}
	while($is_exists && ((microtime() - $start_time) < 10000));*/
	@unlink($filepath);
	if(!file_exists($filepath))
		$is_success = true;
	return $is_success;
}

/**
 * 获取缓存的数据
 * @param string $key 缓存键名 如果有目录 格式为 目录1/目录2/.../键名
 * @return mixed
 */
function getCache($key)
{
	static $caches = array();
	if(!isset($caches[$key]))
	{
		if(!file_exists(PUBLIC_ROOT.'./data/caches/custom/'.$key.'.cache.php'))
			return NULL;
		else
		{
			include(PUBLIC_ROOT.'./data/caches/custom/'.$key.'.cache.php');
			$list = explode('/',$key);
			$key = end($list);
			$caches[$key] = $data[$key];
		}
	}
	return $caches[$key];
}

/**
 * 设置缓存数据
 * @param string $key 缓存键名 可设置所在目录 格式为 目录1/目录2/.../键名
 * @param string $data 缓存的数据
 * @return bool
 */
function setCache($key,$data)
{
	$cache_path = PUBLIC_ROOT.'./data/caches/custom/'.$key.'.cache.php';
	$phth = dirname($cache_path);
	makeDir($phth);
	$list = explode('/',$key);
	$key = end($list);
    $cache_data = "<?php\n".'$data[\''.$key."'] = ".var_export($data, true).";\n\n?>";
	return writeFile($cache_path,$cache_data);
}

/**
 * 删除缓存
 * @param string $key 缓存键名 如果有目录 格式为 目录1/目录2/.../键名
 * @return bool
 */
function deleteCache($key)
{
    return removeFile(PUBLIC_ROOT.'./data/caches/custom/'.$key.'.cache.php');
}

/**
 * 清空缓存目录
 * @param string $dir 缓存目录
 * @return void
 */
function clearCacheDir($dir)
{
    clearDir(PUBLIC_ROOT.'./data/caches/custom/'.$dir,true);
}

function avatar($uid, $type = 'm',$code = '', $is_src = 0,$is_full = false)
{
	static $avatars = array();
	static $types = array(
		's'=>'small',
		'm'=>'middle',
		'b'=>'big',
	);

	$size = 'small';
	if(array_key_exists($type,$types))
		$size = $types[$type];

	if($avatars[$uid][$size] === NULL)
	{
		global $_FANWE;
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		if(empty($code))
		{
			$file_path = PUBLIC_ROOT.'./upload/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_'.$size.'.jpg';
			$file = './public/upload/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_'.$size.'.jpg';
			if(!file_exists($file_path))
				$file = './public/upload/avatar/noavatar_'.$size.'.jpg';
		}
		else
		{
			$file = './'.$code.'/public/upload/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_'.$size.'.jpg';
		}
		$avatars[$uid][$size] = $file;
	}
	
	$src = $avatars[$uid][$size];
	if($is_full)
		$src = FS("Image")->getImageUrl($src,2);

	return $is_src ? $src : '<img src="'.$src.'" />';
}

/**
 * 根据图片原图地址。 获取规格图片的地址，如果没有该规则动态生成。
 * by fzmatthew
 */
function getImgName($img_url,$width=0,$height=0,$gen = 0,$is_full = false)
{
	static $imagec = NULL;
	if($width>0&&$height>0)
	{
		if(strpos($img_url,'./public/') === FALSE)
		{
			$img_url_arr[0] = substr($img_url,0,-4);
			$img_url_arr[1] = substr($img_url,-3,3);
			$img_url = $img_url_arr[0]."_".$width."x".$height.".".$img_url_arr[1];
			if($is_full)
				$img_url = FS("Image")->getImageUrl($img_url);
			$img_url .= '?gen='.$gen;
		}
		else
		{
			if(!file_exists(FANWE_ROOT.$img_url))
				return;
			
			if($imagec === NULL)
			{
				include_once fimport('class/image');
				$imagec = new Image();
			}
			
			$org_img = $img_url;
			$img_url_arr[0] = substr($img_url,0,-4);
			$img_url_arr[1] = substr($img_url,-3,3);
			$img_url = $img_url_arr[0]."_".$width."x".$height.".".$img_url_arr[1];
			if(!file_exists(FANWE_ROOT.$img_url))
			{
				$imagec->thumb(FANWE_ROOT.$org_img,$width,$height,$gen);
			}

			if($is_full)
				$img_url = FS("Image")->getImageUrl($img_url,2);
		}
	}
	return $img_url;
}

/**
 *
 * @param $origin_path 原始物理图片地址
 * @param $path 存储的路径
 * @param $file_name 保存的文件名
 * @param $del_temp 是否删除临时文件
 * @param $id 关联编号，将根据编号生成目录
 *
 * 返回 复制成功的信息,如为false则复制失败
 * array(
 * 	'path'	=>	xxx  //物理路径
 *  'url'	=>	xxx  //相对路径
 * );
 *
 * by fzmatthew
 */
function copyFile($origin_path, $path = 'share',$del_temp = true,$id = 0)
{
	static $image = NULL;
	if($image === NULL)
	{
		include_once fimport('class/image');
		$image = new Image();
	}
	
	if($path == 'temp')
		$dir = './public/upload/temp/'.fToDate(NULL,'Y/m/d/H');
	else
	{
		if($id > 0)
			$dir = './public/upload/'.$path.'/'.getDirsById($id);
		else
			$dir = './public/upload/'.$path.'/'.fToDate(NULL,'Y/m/d');
	}

	$file_name = md5(microtime(true)).random('6').'.jpg';
	makeDir(FANWE_ROOT.$dir);
	$file_path = FANWE_ROOT.$dir."/".$file_name;

	if(file_exists($origin_path) && @copy($origin_path,$file_path))
	{
		if($del_temp)
			@unlink($origin_path);
	}
	else
	{
		$data = getUrlContent($origin_path);
		if(!empty($data) && @file_put_contents($file_path,$data) > 0)
		{
			if($del_temp)
				@unlink($origin_path);
		}
		else
			return false;
	}

	$info  = $image->getImageInfo($file_path);
	if($info['type'] != 'jpg' && $info['type'] != 'jpeg')
	{
		if(!$image->convertType($file_path,$file_path))
			return false;
	}

	return array(
		'path' => $file_path,
		'url' => $dir."/".$file_name,
		'width' => $info[0],
		'height' => $info[1],
	);
}

/**
 *
 * @param $origin_path 原始物理图片地址
 * @param array $sizes 缩略图大小信息 为空则取后台设置
	 	可生成多个缩略图
		数组 参数1 为宽度，
			 参数2为高度，
			 参数3为处理方式:0(缩放,默认)，1(剪裁)，
			 参数4为是否水印 默认为 0(不生成水印)
	 	array(
			array(300,300,0,0),
			array(100,100,0,0),
			...
		)，
 * @param $path 存储的路径
 * @param $file_name 保存的文件名
 * @param $del_temp 是否删除临时文件
 * @param $id 关联编号，将根据编号生成目录
 *
 * 返回 复制成功的信息,如为false则复制失败
 * array(
 * 	'path'	=>	xxx  //物理路径
 *  'url'	=>	xxx  //相对路径
 * );
 *
 * by fzmatthew
 */
function copyImage($origin_path,$sizes = array(),$path = 'share', $del_temp = true,$id = 0)
{
	global $_FANWE;
	static $size_setting = NULL,$image = NULL;
	if($image === NULL)
	{
		include_once fimport('class/image');
		$image = new Image();
	}

	if($path == 'temp')
		$dir = './public/upload/temp/'.fToDate(NULL,'Y/m/d/H');
	else
	{
		if($id > 0)
			$dir = './public/upload/'.$path.'/'.getDirsById($id);
		else
			$dir = './public/upload/'.$path.'/'.fToDate(NULL,'Y/m/d');
	}

	makeDir(FANWE_ROOT.$dir);

	$file_name = md5(microtime(true)).random('6').'.jpg';
	$file_path = FANWE_ROOT.$dir."/".$file_name;

	$bln = false;
	if(file_exists($origin_path) && @copy($origin_path,$file_path))
		$bln = true;
	else
	{
		$data = getUrlContent($origin_path);
		if(!empty($data) && @file_put_contents($file_path,$data) > 0)
			$bln = true;
	}

	if($bln)
	{
		$info  = $image->getImageInfo($file_path);
		if($info['type'] != 'jpg' && $info['type'] != 'jpeg')
		{
			if(!$image->convertType($file_path,$file_path))
				return false;
		}

		$water_image = $_FANWE['setting']['water_image'];
		if(!empty($water_image))
			$water_image = FANWE_ROOT.$water_image;
		
		$water_mark = intval($_FANWE['setting']['water_mark']);
		$alpha = intval($_FANWE['setting']['water_alpha']);
		$place = intval($_FANWE['setting']['water_position']);

		if($sizes !== false && empty($sizes))
		{
			if($size_setting === NULL)
			{
				if(!isset($_FANWE['cache']['image_sizes']))
					FanweService::instance()->cache->loadCache('image_sizes');
				$size_setting = $_FANWE['cache']['image_sizes'];
			}

			$sizes = $size_setting;
		}

		foreach($sizes as $size)
		{
			if($size[0] > 0 || $size[1] > 0)
			{
				$thumb_bln = false;
				$thumb_type = isset($size[2]) ? intval($size[2]) : 0;
				if($thumb = $image->thumb($file_path,$size[0],$size[1],$thumb_type))
				{
					if(isset($size[3]) && intval($size[3]) > 0)
						$image->water($thumb['path'],$water_image,$alpha, $place);
				}
			}
		}

		if($del_temp)
			@unlink($origin_path);

		return array(
			'path' => $file_path,
			'url' => $dir."/".$file_name,
			'width' => $info[0],
			'height' => $info[1],
		);
	}
	else
		return false;
}

//获取url的内容
function getUrlContent($url)
{
	$content = '';
	if(!parseUrl($url))
	{
		$content = @file_get_contents($url);
	}
	else
	{
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_TIMEOUT,30);
			curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
			curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($ch);
			curl_close($ch);
		}
		else
		{
			$content = @file_get_contents($url);
		}
	}

	return $content;
}

/**
 * 搜索Club的分类
 *
 * @param 父类ID $pid
 * @return 数组
 */

function getForumClass($pid = 0)
{
	global $_FANWE;
	FanweService::instance()->cache->loadCache('forums');
	$list = $_FANWE['cache']['forums'];
	if($pid > 0){
		foreach($list as $k => $v)
		{
			if(intval($pid)==$v['fid'])
				return $v;
		}
	}
	else {
		return $list;
	}
}

function getAskClass($pid = 0)
{
	global $_FANWE;
	FanweService::instance()->cache->loadCache('asks');
	$list = $_FANWE['cache']['asks'];
	if($pid > 0){
		foreach($list as $k => $v)
		{
			if(intval($pid)==$v['aid'])
				return $v;
		}
	}
	else {
		return $list;
	}
}
/**
 * 获取CDN链接
 * @param string $url 链接
 * @param string $type (image:图片,css:样式,js:脚本),为空则根据后缀获取
 * @return string
 */

function getCDNUrl($url,$type='')
{
	global $_FANWE;
	static $img_exts = array('jpg', 'jpeg', 'png', 'bmp','gif','giff'),
		   $types = array('image','css','js');

	if(empty($type))
	{
		$ext = fileExt($url);
		if(in_array($ext,$img_exts))
			$type = 'image';
		elseif($ext == 'css')
			$type = 'css';
		elseif($ext == 'js')
			$type = 'js';
	}

	$url_pre = $_FANWE['site_root'];
	if(in_array($type,$types))
	{
		switch($type)
		{
			case 'image':
				if(!empty($_FANWE['config']['cdn']['image']))
					$url_pre = $_FANWE['config']['cdn']['image'];
			break;

			case 'css':
				if(!empty($_FANWE['config']['cdn']['css']))
					$url_pre = $_FANWE['config']['cdn']['css'];
			break;

			case 'js':
				if(!empty($_FANWE['config']['cdn']['js']))
					$url_pre = $_FANWE['config']['cdn']['js'];
			break;
		}
	}

	return $url_pre.$url;
}

/**
 * 获取文件扩展名
 * @return string
 */
function fileExt($file)
{
	return addslashes(strtolower(substr(strrchr($file, '.'), 1, 10)));
}

function priceFormat($price)
{
	return '¥'.number_format(round($price,2),2);
}

/**
 * 根据ID划分目录
 * @return string
 */
function getDirsById($id)
{
	$id = sprintf("%011d", $id);
	$dir1 = substr($id, 0, 3);
	$dir2 = substr($id, 3, 3);
	$dir3 = substr($id, 6, 3);
	$dir4 = substr($id, -2);
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.$dir4;
}

function cssParse($urls)
{
	global $_FANWE;
	if(!empty($urls))
		$_FANWE['page_parses']['css'][] = $urls;
	
	if(is_array($urls))
	{
		$url = md5(implode(',',$urls));
		$css_url = './public/data/tpl/css/'.$url.'.css';
		$url_path = FANWE_ROOT.$css_url;
		if(!file_exists($url_path))
		{
			$css_content = '';
			foreach($urls as $url)
			{
				$url = str_replace('./tpl/css/','./public/data/tpl/css/',$url);
				$css_content .= @file_get_contents(FANWE_ROOT.$url);
			}
			$css_content = preg_replace("/[\r\n]/",'',$css_content);
			@file_put_contents($url_path,$css_content);
		}

		return $css_url;
	}
	else
	{
		return $urls;
	}
}

function scriptParse($urls)
{
	global $_FANWE;
	if(!empty($urls))
		$_FANWE['page_parses']['script'][] = $urls;
	
	if(is_array($urls))
	{
		$url = md5(implode(',',$urls));
		$js_url = './public/data/tpl/js/'.$url.'.js';
		$url_path = FANWE_ROOT.$js_url;
		if(!file_exists($url_path))
		{
			$js_content = '';
			foreach($urls as $url)
			{
				$url = str_replace('./tpl/js/','./tpl/'.TMPL.'/js/',$url);
				$js_content .= @file_get_contents(FANWE_ROOT.$url)."\r\n";
			}

			@file_put_contents($url_path,$js_content);
		}

		return $js_url;
	}
	else
	{
		return $urls;
	}
}

/**
 * 获取是否显示前台管理
 * @return bool
 */
function getIsManage($module)
{
	global $_FANWE;
	if($_FANWE['uid'] == 0)
		return false;

	$module = strtolower($module);
	if(isset($_FANWE['authoritys'][$module]))
		return true;
	else
		return false;
}

/**
 * 检测是否具有指定的前台管理权限
 * @return bool
 */
function checkAuthority($module,$action)
{
	global $_FANWE;
	if($_FANWE['uid'] == 0)
		return false;

	$module = strtolower($module);
	$action = strtolower($action);

	if(isset($_FANWE['authoritys'][$module]) && isset($_FANWE['authoritys'][$module][$action]))
		return true;
	else
		return false;
	return true;
}

/**
 * 检测所管理的对像是否锁定,已锁定返回锁定数据array,未锁定返回false
   如果锁定30分钟以上,还未解锁,将设为未锁定
 * @return
 */
function checkIsManageLock($module,$id)
{
	global $_FANWE;
	$module = strtolower($module);
	$lock_file = PUBLIC_ROOT.'./manage/'.$module.'/'.$id.'.lock';
	if(file_exists($lock_file))
	{
		include $lock_file;

		if(TIME_UTC - $lock['time'] > 1800)
		{
			removeFile($lock_file);
			return false;
		}
		if($lock['uid'] == $_FANWE['uid'])
			return false;
		else
			return $lock;
	}
	else
		return false;
}

/**
 * 获取前台管理锁定
 * @return void
 */
function getManageLock($module,$id)
{
	$module = strtolower($module);
	$lock_file = PUBLIC_ROOT.'./manage/'.$module.'/'.$id.'.lock';
	if(file_exists($lock_file))
	{
		include $lock_file;
		return $lock;
	}
	else
		return false;
}

/**
 * 创建前台管理锁定
 * @return void
 */
function createManageLock($module,$id)
{
	global $_FANWE;
	$module = strtolower($module);
	$phth = PUBLIC_ROOT.'./manage/'.$module;
	makeDir($phth);
	$lock_file = $phth.'/'.$id.'.lock';
	$data = array(
		'uid'=>$_FANWE['uid'],
		'user_name'=>$_FANWE['user_name'],
		'time'=>TIME_UTC,
	);
	$data = "<?php\n".'$lock = '.var_export($data, true).";\n?>";
	return writeFile($lock_file,$data);
}

/**
 * 删除前台管理锁定
 * @return void
 */
function deleteManageLock($module,$id)
{
	$module = strtolower($module);
	$lock_file = PUBLIC_ROOT.'./manage/'.$module.'/'.$id.'.lock';
	removeFile($lock_file);
}

/**
 * 前台管理日志
 * @return void
 */
function createManageLog($module,$action,$id,$content = '')
{
	global $_FANWE;

	$log = array(
		'rec_id'=>$id,
		'module'=>$module,
		'action'=>$action,
		'uid'=>$_FANWE['uid'],
		'user_name'=>$_FANWE['user_name'],
		'content'=>$content,
		'create_time'=>TIME_UTC
	);

	FDB::insert('manage_log',$log);
}

/**
 * 获取分享链接
 * @return string
 */
function getSnsLink($types,$title,$url,$content,$pic)
{
	global $_FANWE;
	$links = array();
	$title = urlencode($title);
	$content_url = urlencode($content.'　'.$url);
	$content = urlencode($content);
	$url = urlencode($url);
	$site_url = urlencode($_FANWE['site_url']);
	$pic = empty($pic) ? $pic : urlencode($pic);
	foreach($types as $type)
	{
		switch($type)
		{
			case 'kaixin':
				$links[$type] = "http://www.kaixin001.com/diary/write.php?classid=0&title=$title&content=$content_url";
			break;
			
			case 'renren':
				$links[$type] = "http://share.renren.com/share/buttonshare.do?link=$url&title=$title";
			break;
			
			case 'sina':
				$links[$type] = "http://v.t.sina.com.cn/share/share.php?sourceUrl=$site_url&content=utf8&url=$url&title=$content";
				if(!empty($pic))
					$links[$type] .= "&pic=$pic";
			break;
			
			case 'tqq':
				$links[$type] = "http://v.t.qq.com/share/share.php?url=$url&title=$content";
				if(!empty($pic))
					$links[$type] .= "&pic=$pic";
			break;
			
			case 'douban':
				$links[$type] = "http://www.douban.com/recommend/?url=$url&title=$title";
			break;
			
			case 'qzone':
				$links[$type] = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=$url&title=$title&summary=$content";
				if(!empty($pic))
					$links[$type] .= "&pics=$pic";
			break;
			
			case 'baidu':
				$links[$type] = "http://apps.hi.baidu.com/share/?url=$url&title=$title&content=$content";
			break;
		}
	}
	
	return $links;
}

function getLoginModule($class_name)
{
	global $_FANWE;
	if(!isset($_FANWE['cache']['logins']))
		FanweService::instance()->cache->loadCache('logins');
		
	if(file_exists(FANWE_ROOT."login/".$class_name.".php"))
	{
		require_once FANWE_ROOT."login/".$class_name.".php";
		if(class_exists($class_name))
		{
			$module = new $class_name;
			return $module->getInfo();
		}
	}
	else
		return false;
}

function getLoginModuleList()
{
	global $_FANWE;
	if(!isset($_FANWE['cache']['logins']))
		FanweService::instance()->cache->loadCache('logins');
	
	$list = array();
	foreach($_FANWE['cache']['logins'] as $class_name => $val)
	{
		if(file_exists(FANWE_ROOT."login/".$class_name.".php"))
		{
			require_once FANWE_ROOT."login/".$class_name.".php";
			if(class_exists($class_name))
			{
				$module = new $class_name;
				$list[$class_name] = $module->getInfo();
			}
		}
	}
	return $list;
}

function deleteShareImg($img_path,$server_code = '')
{
	if(empty($server_code))
	{
		$img_path = FANWE_ROOT.str_replace('./','',$img_path);
		$paths = pathinfo($img_path);
		@unlink($img_path);
		$old_img = explode('.',$img_path);
		$old_img = $old_img[0];
		if($dirhandle = opendir($paths['dirname']))
		{
			while(($file = readdir($dirhandle)) !== FALSE)
			{
				if(($file!=".") && ($file!=".."))
				{
					$filename = $paths['dirname'].'/'.$file;
					if(strpos($filename,$old_img) !== FALSE)
					{
						@unlink($filename);
					}
				}
			}
			@closedir($dirhandle);
		}
	}
	else
	{
		$server = FS("Image")->getServer($server_code);
		if($server)
		{
			$args = array();
			$args['img_path'] = $img_path;
			$server = FS("Image")->getImageUrlToken($args,$server,1);
			FS("Image")->sendRequest($server,'deleteshareimg');
		}
	}
}

function deleteImg($img_path,$server_code = '')
{
	if(empty($server_code))
	{
		$img_path = FANWE_ROOT.str_replace('./','',$img_path);
		@unlink($img_path);
	}
	else
	{
		$server = FS("Image")->getServer($server_code);
		if($server)
		{
			$args = array();
			$args['img_path'] = $img_path;
			$server = FS("Image")->getImageUrlToken($args,$server,1);
			FS("Image")->sendRequest($server,'deleteimg');
		}
	}
}

/**
 * 清除当前2小时以前的临时图片
 * @param string $dir 目录路径
 * @return void
 */
function clearTempImage($path = '')
{
	global $_FANWE;
	if($path == '')
		$_FANWE['clear_image_count'] = 0;

	$max_time = mktime(date('H')-2,0,0,date('m'),date('d'),date('Y')) - date('Z');
	$basepath = FANWE_ROOT.'public/upload/temp/';
	$paths = array();
	if($path != '')
	{
		$currentpath = str_replace($basepath,'',$path);
		$paths = explode('/',$currentpath);
		$year = (int)$paths[0];
		$month = isset($paths[1]) ? (int)$paths[1] : 1;
		$day = isset($paths[2]) ? (int)$paths[2] : 1;
		$hours = isset($paths[3]) ? (int)$paths[3] : 0;
		$day_time = mktime($hours,0,0,$month,$day,$year) - date('Z');
		if($max_time <= $day_time)
			return;

		$currentpath .= '/';
	}
	else
		$currentpath = '';

	$dir = $basepath.$currentpath;
	$directory = dir($dir);
	while($entry = @$directory->read())
	{
		if($_FANWE['clear_image_count'] >= 50)
			break;

		if($entry != '.' && $entry != '..')
		{
			$filename = $dir.$entry;
			if(is_dir($filename))
			{
				clearTempImage($filename);
				@rmdir($filename);
			}

			if(is_file($filename))
			{
				removeFile($filename);
				$_FANWE['clear_image_count']++;
			}
		}
	}
	$directory->close();
}
?>