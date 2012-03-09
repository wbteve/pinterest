<?php 
define('MODULE_NAME','User');

$actions = array('login','logout','ajax_login','register','ajax_register','forgetpassword','resetpassword','interest','followdaren','agreement','bind','savebind');

if(isset($_REQUEST['action']))
{
	$action = strtolower($_REQUEST['action']);
	if(!in_array($action,$actions))
		$action = 'login';
}

define('ACTION_NAME',$action);

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/user');

switch(ACTION_NAME)
{
	case 'logout':
		UserModule::logout();
	break;
	case 'login':
		UserModule::login();
	break;
	case 'ajax_login':
		UserModule::ajaxLogin();
	break;
	case 'register':
		UserModule::register();
	break;
	case 'ajax_register':
		UserModule::ajaxRegister();
	case 'forgetpassword':
		UserModule::forgetPassword();
	break;
	case 'resetpassword':
		UserModule::resetPassword();
	break;
	case 'interest':
		UserModule::interest();
	break;
	case 'agreement':
		UserModule::agreement();
	break;
	case 'bind':
		UserModule::bind();
	break;
	case 'savebind':
		UserModule::saveBind();
	break;
}
?>