<?php 
define('MODULE_NAME','Exchange');

$actions = array('index','rule');
$action = 'index';

if(isset($_REQUEST['action']))
{
	$action = strtolower($_REQUEST['action']);
	if(!in_array($action,$actions))
		$action = 'index';
}

define('ACTION_NAME',$action);

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();
require fimport('module/exchange');

$_FANWE['nav_title'] = lang('common','exchange');

switch(ACTION_NAME)
{
	case 'index':
		ExchangeModule::index();
	break;
	case 'rule':
		ExchangeModule::rule();
	break;
}
?>