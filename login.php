<?php
define('MODULE_NAME','login');
require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->cache_list[] = 'logins';
$fanwe->initialize();

if(isset($_REQUEST['mod']))
{
	$class_name = addslashes(trim($_REQUEST['mod']));
    checkClass($class_name);
	require FANWE_ROOT."login/".$class_name.".php";
	if(class_exists($class_name))
	{
		$module = new $class_name;		
		$module->loginJump();
	}
	exit;
}


if(isset($_REQUEST['bind']))
{
	$class_name = addslashes(trim($_REQUEST['bind']));
    checkClass($class_name);
	require FANWE_ROOT."login/".$class_name.".php";
	if(class_exists($class_name))
	{
		$module = new $class_name;
		$module->bindJump();
	}
	exit;
}

if(isset($_REQUEST['unbind']))
{
	$class_name = addslashes(trim($_REQUEST['unbind']));
    checkClass($class_name);
	require FANWE_ROOT."login/".$class_name.".php";
	if(class_exists($class_name))
	{
		$module = new $class_name;
		$module->unBind();
	}
	exit;
}

//同步微博
if(isset($_REQUEST['loop']))
{
	$uid = intval($_REQUEST['uid']);
	echo syn_weibo($uid);
}

function syn_weibo($uid)
{
	global $_FANWE;
	static $mods = array();
	$_FANWE['uid'] = $uid;
	$weibos = FDB::fetchAll("select * from ".FDB::table("pub_schedule")." where uid = ".$uid." order by type");
	FDB::query("delete from ".FDB::table("pub_schedule")." where uid = ".$uid);
	foreach($weibos as $weibo)
	{
		if(file_exists(FANWE_ROOT."login/".$weibo['type'].".php"))
		{
			if(!isset($mods[$weibo['type']]))
			{
				require_once FANWE_ROOT."login/".$weibo['type'].".php";
				$class =$weibo['type'];
				$mods[$weibo['type']] = new $class;
			}

			$data = unserialize(base64_decode($weibo['data']));
			$mods[$weibo['type']]->sendMessage($data);
		}
	}
}

function checkClass($str)
{
	global $_FANWE;
	if(!isset($_FANWE['cache']['logins'][$str]))
        exit;
}
?>