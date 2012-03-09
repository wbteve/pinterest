<?php 
include "base.php";
require_once FANWE_ROOT."sdks/taobao/taobao.func.php";
$top_appkey = $_FANWE['cache']['logins']['taobao']['app_key'];
$top_secret = $_FANWE['cache']['logins']['taobao']['app_secret'];

$top_parameters = $_REQUEST['top_parameters'];
$top_session = $_REQUEST['top_session'];
$top_sign = $_REQUEST['top_sign'];

if(!CheckTaoBaoSign($top_appkey,$top_secret,$top_parameters,$top_session,$top_sign))
	exit;

require_once FANWE_ROOT."core/class/user/taobao.class.php";
$parameters = GetTaoBaoParameters($top_parameters);

$tu = new TaobaoUser();
switch($callback_type)
{
	case 'login':
		$tu->loginHandler($parameters,$top_session);
		$url = FU('u/index');
	break;
	
	case 'bind':
		$tu->bindHandler($parameters,$top_session);
		$url = FU('settings/bind');
	break;
	
	case 'buyer':
		$tu->buyerHandler($parameters,$top_session);
		$url = FU('settings/buyerverifier');
	break;
}

fSetCookie('callback_type','');
fHeader("location:".$url);
?>