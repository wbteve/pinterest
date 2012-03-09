<?php
define('MODULE_NAME', 'mapi');
require dirname(__FILE__) . '/core/service/fanwe.service.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

$i_type = 0;//上传数据格式类型; 0:base64;1;REQUEST;2:json
//r_type: 返回数据格式类型; 0:base64;1;json_encode;2:array
if (isset($_REQUEST['i_type']))
{
	$i_type = intval($_REQUEST['i_type']);
}

if ($i_type == 1)
{
	$requestData = $_REQUEST;
}
else
{
	if (isset($_REQUEST['requestData']))
	{
		if ($i_type == 2){
			$requestData = json_decode(trim($_REQUEST['requestData']), 1);
		}else{
			$requestData = base64_decode(trim($_REQUEST['requestData']));
			$requestData = json_decode($requestData, 1);
		}
	}else{
		$requestData = $_REQUEST;
	}
}

$email = $_FANWE['requestData']['email'];
$pwd = md5($_FANWE['requestData']['pwd']);
$user = FDB::fetchFirst("SELECT u.*, uc.*, us.*, up.* FROM ".FDB::table('user')." u
			LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
			LEFT JOIN ".FDB::table('user_status')." us USING(uid)
			LEFT JOIN ".FDB::table('user_profile')." up USING(uid)
			WHERE (u.user_name='$email' OR u.email = '$email') AND u.password = '$pwd'");

if($user)
{
	$_FANWE['user'] = $user;
	$_FANWE['uid'] = $user['uid'];
	$_FANWE['user_name'] = $user['user_name'];
	$_FANWE['user_avatar'] = avatar($user['uid'],'m',$user['server_code'],1,true);
	$_FANWE['gid'] = $user['gid'];
}
else
	$_FANWE['user'] = false;

$action = $requestData['act'];
$actions = array('init');
define('ACTION_NAME', $action);

//echo dirname(__FILE__). '/core/mapi/base.mapi.php';
require_once dirname(__FILE__). '/core/mapi/base.mapi.php';
//require fimport('mapi/base');
//echo 'aaa';exit;
require_once dirname(__FILE__). '/core/mapi/'.$action.'.mapi.php';
//require fimport('mapi/'.$action);
$_FANWE['requestData'] = $requestData;
$_FANWE['MConfig'] = m_getMConfig();//初始化配送数据
define('PAGE_SIZE', 6);
$mapi_class = $action."Mapi";
$module = new $mapi_class;
$module->run();

?>