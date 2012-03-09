<?php 
include "base.php";
$login_oauth = unserialize(authcode($_FANWE['cookie']['login_oauth'], 'DECODE'));
if(empty($login_oauth))
	exit;

$oauth_token = $_REQUEST['oauth_token'];
$oauth_verifier = $_REQUEST['oauth_verifier'];

if(empty($oauth_verifier))
	exit;

require_once FANWE_ROOT."core/class/user/sina.class.php";
$sina = new SinaUser();
switch($callback_type)
{
	case 'login':
		$sina->loginHandler($login_oauth,$oauth_verifier);
		$url = FU('u/index');
	break;
	
	case 'bind':
		$sina->bindHandler($login_oauth,$oauth_verifier);
		$url = FU('settings/bind');
	break;
}

fSetCookie('callback_type','');
fSetCookie('login_oauth','');
fHeader("location:".$url);
?>