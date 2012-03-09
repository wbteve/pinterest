<?php 
include "base.php";
require_once FANWE_ROOT."sdks/qq/qq.func.php";
$appid = $_FANWE['cache']['logins']['qq']['app_key'];
$appkey = $_FANWE['cache']['logins']['qq']['app_secret'];
$access_token = getQqAccessToken($appid,$appkey);
$openid = getQqOpenid($access_token);

require_once FANWE_ROOT."core/class/user/qq.class.php";

$qq = new QqUser();
switch($callback_type)
{
	case 'login':
		$qq->loginHandler($access_token,$openid);
		$url = FU('u/index');
	break;
	
	case 'bind':
		$qq->bindHandler($access_token,$openid);
		$url = FU('settings/bind');
	break;
}

fSetCookie('callback_type','');
fHeader("location:".$url);
?>