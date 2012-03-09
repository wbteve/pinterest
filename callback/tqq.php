<?php 
include "base.php";
$login_oauth = unserialize(authcode($_FANWE['cookie']['login_oauth'], 'DECODE'));
if(empty($login_oauth))
	exit;

$oauth_token = $_REQUEST['oauth_token'];
$oauth_verifier = $_REQUEST['oauth_verifier'];

if(empty($oauth_token) || empty($oauth_verifier))
	exit;
	
$_FANWE['login_oauth']['tqq'] = $login_oauth;
require_once FANWE_ROOT."core/class/user/tqq.class.php";
$tqq = new TqqUser();
if(!OpenSDK_Tencent_Weibo::getAccessToken($oauth_verifier))
	exit;

switch($callback_type)
{
	case 'login':
		$tqq->loginHandler();
		$url = FU('u/index');
	break;
	
	case 'bind':
		$tqq->bindHandler();
		$url = FU('settings/bind');
	break;
}

fSetCookie('callback_type','');
fSetCookie('login_oauth','');
fHeader("location:".$url);
?>